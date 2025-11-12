#!/bin/bash
# Script pour appliquer les 5 commits du frontend AGROCEAN

echo "🔧 Application des patches frontend..."
echo ""

# Vérifier qu'on est dans le bon dossier
if [ ! -d ".git" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Agrocean_frontend"
    exit 1
fi

# Vérifier qu'on est sur la bonne branche
current_branch=$(git branch --show-current)
if [ "$current_branch" != "claude/frontend-agrocean-011CV2yGSXXKjvBtK5XknVBL" ]; then
    echo "⚠️  Branche actuelle: $current_branch"
    echo "📌 Basculement vers claude/frontend-agrocean-011CV2yGSXXKjvBtK5XknVBL..."
    git checkout claude/frontend-agrocean-011CV2yGSXXKjvBtK5XknVBL || exit 1
fi

# Compter les commits en avance
ahead=$(git rev-list --count origin/claude/frontend-agrocean-011CV2yGSXXKjvBtK5XknVBL..HEAD 2>/dev/null || echo "0")
echo "📊 Commits locaux en avance: $ahead"

if [ "$ahead" -eq "0" ]; then
    echo "✅ Aucun commit à pusher - tout est à jour!"
    exit 0
fi

# Pusher les commits
echo ""
echo "🚀 Push des $ahead commits vers GitHub..."
git push -u origin claude/frontend-agrocean-011CV2yGSXXKjvBtK5XknVBL

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ SUCCESS! Tous les commits ont été pushés sur GitHub"
    echo "📝 Commits pushés:"
    git log --oneline -5
else
    echo ""
    echo "❌ Échec du push"
    echo "Vérifiez votre connexion et vos permissions GitHub"
    exit 1
fi
