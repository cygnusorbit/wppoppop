'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const values = new Map();
const handlers = new Map();
const calls = [];
const requests = [];
const document = {};
function $(selector) {
    if (selector && selector.jquery) return selector;
    const key = typeof selector === 'string' && !values.has(selector)
        ? selector.split(',').map(part => part.trim()).find(part => values.has(part)) : selector;
    return {
        jquery: true,
        length: values.has(key) ? 1 : 0,
        val(value) {
            if (arguments.length) { values.set(selector, value); return this; }
            return values.get(key);
        },
        off(event) { handlers.delete(event); return this; },
        on(event, target, callback) { handlers.set(event, callback || target); return this; },
        trigger(event) { calls.push(event); return this; },
        prop() { return this; },
        html() { return this; },
        css() { return this; },
        removeClass() { return this; },
        addClass() { return this; },
        fadeIn() { return this; },
        fadeOut() { return this; },
        hide() { return this; },
        show() { return this; },
        append() { return this; },
        hasClass() { return false; }
    };
}
$.ajax = options => requests.push(options);
const context = vm.createContext({
    jQuery: $, document, navigator: { platform: 'Linux' },
    URL, URLSearchParams, setTimeout: callback => callback(),
    alert: message => calls.push(message)
});
context.window = context;
context.location = { search: '', href: 'https://example.test/wp-admin/admin.php?page=wppoppop-builder' };
context.history = { replaceState() {} };
context.wppoppop_vars = { nonce: 'test-nonce', ajax_url: '/wp-admin/admin-ajax.php' };
const builderDir = path.join(__dirname, '..', 'admin', 'js', 'builder');
for (const file of ['builder-core.js', 'builder-canvas.js', 'builder-io.js']) {
    vm.runInContext(fs.readFileSync(path.join(builderDir, file), 'utf8'), context, { filename: file });
}
const core = context.WpPopPopBuilderCore;
const canvas = context.WpPopPopBuilderCanvas;
const io = context.WpPopPopBuilderIO;
const plain = value => JSON.parse(JSON.stringify(value));

let registryFlushes = 0;
let inspectorFlushes = 0;
context.WpPopPopElementsRegistry = {
    flushElementFromDOM() {
        assert.ok(calls.includes('change'));
        registryFlushes++;
        canvas.getActiveElements()[0].content = 'Edited content';
    }
};
context.WpPopPopBuilderInspector = {
    flushActiveElement() { inspectorFlushes++; },
    close() { calls.push('close'); },
    open(id) { calls.push('open:' + id); }
};
context.WpPopPopBuilderSettings = {
    getSettings() { return { triggers: { load: true } }; },
    setSettings(settings) { calls.push(settings.triggers.load); },
    applyActiveCanvasBackground() {},
    syncCurrentCanvasSettings() {},
    renderCanvasBullets() {},
    closeDrawer() {}
};
context.WpPopPopBuilderLayers = {
    deleteLayer(id) { calls.push('delete:' + id); },
    highlightLayer() {},
    renderLayers() {}
};
values.set('#wppoppop-builder-title', 'Edited title');
values.set('#wppoppop-builder-uid', '');
values.set('#prop-padding-top', '12');
values.set('#prop-padding-right', '-5');
values.set('#prop-padding-bottom', 'invalid');
values.set('#prop-padding-left', '7');
values.set('#set-canvas-anim-duration, #set-anim-duration', '0');
values.set('#set-canvas-anim-delay, #set-anim-delay', '250');
core.state.canvases = { 1: [{ id: 'a', type: 'text' }], 2: [{ id: 'b', padding: 99 }] };
core.state.canvasMeta = { 1: {}, 2: {} };
for (const id of ['a', 'el-a', 'wppoppop-el-a', '#el-a']) {
    core.state.activeId = id;
    const config = io.exportConfig();
    const element = config.canvases[1][0];
    assert.equal(element.content, 'Edited content');
    assert.equal(element.paddingTop, 12);
    assert.equal(element.padding_top, 12);
    assert.equal(element.paddingRight, 0);
    assert.equal(element.paddingBottom, 0);
    assert.equal(element.paddingLeft, 7);
    assert.equal(config.canvases[2][0].padding, 99);
    assert.equal(config.canvasMeta[1].anim_duration, 0);
    assert.equal(config.canvasMeta[1].anim_delay, 250);
    assert.equal(config.title, 'Edited title');
    assert.equal(config.settings.triggers.load, true);
    element.paddingTop = 999;
    assert.equal(core.state.canvases[1][0].paddingTop, 12);
}
assert.equal(registryFlushes, 4);
assert.equal(inspectorFlushes, 4);
values.delete('#prop-padding-top');
values.set('#prop-pad-top', '18');
assert.equal(io.exportConfig().canvases[1][0].paddingTop, 18);

io.bindSave();
io.bindSave();
assert.equal(handlers.size, 1);
handlers.get('click.wppoppop_save').call($('#wppoppop-btn-save'), { preventDefault() {} });
assert.equal(requests.length, 1);
assert.equal(registryFlushes, 6);
assert.equal(inspectorFlushes, 6);
const save = requests.pop();
assert.equal(save.data.action, 'wppoppop_save_popup');
assert.equal(save.data.data, save.data.config);
const saved = JSON.parse(save.data.data);
assert.deepEqual(saved.screens, saved.canvases);
assert.deepEqual(saved.canvas_meta, saved.canvasMeta);
assert.equal(saved.canvases[1][0].padding_left, 7);
save.success({ success: true, data: { uid: 'saved-popup' } });
assert.equal(io.getUid(), 'saved-popup');
save.error();
assert.ok(calls.includes('Server error while saving popup.'));

canvas.renderCanvas = () => calls.push('render');
io.loadInitialData();
const load = requests.pop();
assert.equal(load.data.action, 'wppoppop_load_popup');
core.state.canvases = {};
load.success({ success: true, data: { title: saved.title, data: JSON.stringify(saved) } });
assert.deepEqual(plain(core.state.canvases), saved.canvases);
assert.equal(core.state.activeId, null);
core.switchCanvas(2);
assert.equal(core.state.currentCanvas, 2);
assert.equal(canvas.getActiveElements()[0].id, 'b');
canvas.selectElement('wppoppop-el-b');
assert.equal(core.state.activeId, 'b');
canvas.deselect();
assert.equal(core.state.activeId, null);
assert.equal(canvas.selectedId, null);
canvas.enterPreviewMode();
assert.equal(canvas.isPreview, true);
canvas.exitPreviewMode();
assert.equal(canvas.isPreview, false);

core.undo = () => calls.push('undo');
core.redo = () => calls.push('redo');
core.bindKeyboardShortcuts();
core.bindKeyboardShortcuts();
const keydown = handlers.get('keydown.wppoppop_shortcuts');
assert.equal(typeof keydown, 'function');
function key(key, options) {
    keydown(Object.assign({ key, ctrlKey: true, target: { tagName: 'DIV' }, preventDefault() {} }, options));
}
key('z');
key('Z', { shiftKey: true });
assert.ok(calls.includes('undo'));
assert.ok(calls.includes('redo'));
const undoCount = calls.filter(call => call === 'undo').length;
key('z', { target: { tagName: 'INPUT' } });
key('z', { target: { tagName: 'DIV', isContentEditable: true } });
assert.equal(calls.filter(call => call === 'undo').length, undoCount);
core.state.activeId = 'b';
key('Delete', { ctrlKey: false });
assert.ok(calls.includes('delete:b'));
key('s');
assert.ok(calls.includes('click'));
key('Escape', { ctrlKey: false });
assert.equal(core.state.activeId, null);
context.navigator.platform = 'MacIntel';
key('z', { ctrlKey: false, metaKey: true });
assert.equal(calls.filter(call => call === 'undo').length, undoCount + 1);

assert.match(canvas.getInnerMarkup({ type: 'divider', dividerStyle: 'double', dividerWeight: 1 }), /border-top:4px double/);
assert.match(canvas.getInnerMarkup({ type: 'icon', iconClass: 'fa-star' }), /class="fas fa-star"/);
assert.match(canvas.getInnerMarkup({ type: 'video', videoUrl: 'https://youtu.be/abcdefghijk' }), /youtube\.com\/embed\/abcdefghijk/);
context.WpPopPopElementsRegistry.renderStage = () => '<div>Module markup</div>';
assert.equal(canvas.getInnerMarkup({ type: 'text' }), '<div>Module markup</div>');
context.WpPopPopElementsRegistry.renderStage = () => '';
assert.equal(canvas.getInnerMarkup({ type: 'text' }), '');
context.WpPopPopElementsRegistry.renderStage = () => null;
assert.match(canvas.getInnerMarkup({ type: 'icon' }), /fa-star/);
assert.equal(context._origBuildInner, undefined);
context.WpPopPopBuilderCore = null;
context.WpPopPopElementsRegistry = null;
assert.deepEqual(plain(io.exportConfig()), {});

const empty = new Proxy({ length: 0 }, {
    get(target, key) {
        if (key === 'length') return 0;
        if (key === 'val' || key === 'attr' || key === 'css') return () => '';
        if (key === 'hasClass' || key === 'is') return () => false;
        return () => empty;
    }
});
const isolated = vm.createContext({
    jQuery: () => empty, document: {}, setTimeout() {}, setInterval() {}, addEventListener() {}
});
isolated.window = isolated;
for (const file of ['builder-inspector.js', 'builder-elements-registry.js']) {
    vm.runInContext(fs.readFileSync(path.join(builderDir, file), 'utf8'), isolated, { filename: file });
}
for (const type of ['title', 'image', 'button', 'text']) {
    isolated.WpPopPopElementsRegistry.syncFamilyTabs(type, {});
}
console.log('Builder save/export, load/switch/selection/preview, keyboard, markup and inspector regressions passed.');
