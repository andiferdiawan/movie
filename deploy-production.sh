#!/usr/bin/env bash
#
# Merge the "master" branch into "production" and push it, which triggers
# the GitHub Actions deploy workflow (.github/workflows/deploy-production.yml).
#
# Usage:
#   ./deploy-production.sh        # asks for confirmation before pushing
#   ./deploy-production.sh -y     # skip the confirmation prompt
#
set -e

REMOTE="origin"
SOURCE_BRANCH="master"
TARGET_BRANCH="production"
SKIP_CONFIRM=false

for arg in "$@"; do
    case "$arg" in
        -y|--yes) SKIP_CONFIRM=true ;;
    esac
done

echo "🔍 Checking working tree status..."
if [ -n "$(git status --porcelain)" ]; then
    echo "❌ You have uncommitted changes. Commit or stash them first:"
    git status --short
    exit 1
fi

STARTING_BRANCH=$(git rev-parse --abbrev-ref HEAD)

echo "📥 Fetching latest refs from $REMOTE..."
git fetch $REMOTE

echo "🔀 Updating $SOURCE_BRANCH..."
git checkout "$SOURCE_BRANCH"
git pull $REMOTE "$SOURCE_BRANCH"

echo "🔀 Updating $TARGET_BRANCH..."
git checkout "$TARGET_BRANCH"
git pull $REMOTE "$TARGET_BRANCH"

echo "🔗 Merging $SOURCE_BRANCH into $TARGET_BRANCH..."
if ! git merge "$SOURCE_BRANCH" --no-edit; then
    echo ""
    echo "❌ Merge conflict. Resolve the conflicts above, then:"
    echo "     git add <resolved files>"
    echo "     git commit"
    echo "     git push $REMOTE $TARGET_BRANCH"
    exit 1
fi

if [ "$SKIP_CONFIRM" = false ]; then
    read -r -p "🚀 Push $TARGET_BRANCH to $REMOTE now? This triggers the production deploy. [y/N] " reply
    case "$reply" in
        [yY][eE][sS]|[yY]) ;;
        *)
            echo "Aborted. $TARGET_BRANCH was updated locally but not pushed."
            git checkout "$STARTING_BRANCH"
            exit 0
            ;;
    esac
fi

echo "🚀 Pushing $TARGET_BRANCH to $REMOTE..."
git push $REMOTE "$TARGET_BRANCH"

echo "↩️  Switching back to $STARTING_BRANCH..."
git checkout "$STARTING_BRANCH"

echo "✅ Done — $SOURCE_BRANCH merged into $TARGET_BRANCH and pushed."
echo "   Watch the deploy at: $(git remote get-url $REMOTE | sed -E 's#git@github.com:#https://github.com/#; s#\.git$##')/actions"
