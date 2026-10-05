def get_cloning_workflow():
    """Returns the step-by-step cloning workflow for Wppoppop."""
    return [
        "1. Reference Identification: Use https://github.com/cygnusorbit/greenpop as the reference for original function & feature.",
        "2. Clone Initialization: Set up the project clone repository at https://github.com/cygnusorbit/wppoppop.",
        "3. Scaffolding Integration: Commit the initial WP Pop Pop plugin scaffold, triggers, and .gitignore file.",
        "4. Development Checkpoint (Version Control): Integrate update_version.py into the Develop stage.",
        "5. Development Checkpoint (Documentation): Update README.md to reflect the current Develop stage."
    ]

if __name__ == "__main__":
    print("--- Wppoppop Cloning Workflow Tracker ---")
    for step in get_cloning_workflow():
        print(step)
