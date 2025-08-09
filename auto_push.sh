#!/bin/bash

# Auto push script for Student Hub AI
# Sử dụng: ./auto_push.sh "commit message"

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Default commit message
COMMIT_MSG="Auto update: $(date '+%Y-%m-%d %H:%M:%S')"

# Use provided message if available
if [ $# -gt 0 ]; then
    COMMIT_MSG="$1"
fi

echo -e "${BLUE}🚀 Starting auto push process...${NC}"
echo -e "${YELLOW}📝 Commit message: $COMMIT_MSG${NC}"

# Check if we're in a git repository
if [ ! -d ".git" ]; then
    echo -e "${RED}❌ Error: Not in a git repository!${NC}"
    exit 1
fi

# Add all changes
echo -e "${BLUE}📦 Adding all changes...${NC}"
git add .

# Check if there are changes to commit
if git diff --cached --quiet; then
    echo -e "${YELLOW}⚠️  No changes to commit.${NC}"
    exit 0
fi

# Show what will be committed
echo -e "${BLUE}📋 Files to be committed:${NC}"
git diff --cached --name-only

# Commit changes
echo -e "${BLUE}💾 Committing changes...${NC}"
git commit -m "$COMMIT_MSG"

# Get current branch
CURRENT_BRANCH=$(git branch --show-current)
echo -e "${BLUE}🌿 Current branch: $CURRENT_BRANCH${NC}"

# Push to remote
echo -e "${BLUE}📤 Pushing to origin/$CURRENT_BRANCH...${NC}"
git push origin "$CURRENT_BRANCH"

# Success message
echo -e "${GREEN}✅ Successfully pushed changes to GitHub!${NC}"
echo -e "${GREEN}🔗 Repository: https://github.com/markprovjp/student-hub-ai${NC}"

# Show git log
echo -e "${BLUE}📜 Latest commits:${NC}"
git log --oneline -5
