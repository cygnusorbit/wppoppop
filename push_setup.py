import subprocess
import os

# 1. Create a suitable .gitignore for WordPress plugin development
gitignore_content = """.DS_Store
Thumbs.db
node_modules/
vendor/
.wp-env.json
.wp-env/
*.log
ref_greenpop/
"""

with open(".gitignore", "w") as f:
    f.write(gitignore_content)
print("Created .gitignore")

# 2. Initialize git repository if not already initialized
if not os.path.exists(".git"):
    subprocess.run(["git", "init"], check=True)
    print("Initialized Git repository.")

# 3. Stage and commit all project files
subprocess.run(["git", "add", "."], check=True)
subprocess.run(["git", "commit", "-m", "Initial commit: WP Pop Pop plugin scaffold and triggers"], check=True)

# 4. Ensure the default branch is named 'main'
subprocess.run(["git", "branch", "-M", "main"], check=True)
print("Ready to push to remote.")
