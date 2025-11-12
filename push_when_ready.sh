#!/bin/bash
echo "🔄 Tentative de push des commits frontend..."
cd /home/user/Agrocean_frontend
git push -u origin claude/frontend-agrocean-011CV2yGSXXKjvBtK5XknVBL
if [ $? -eq 0 ]; then
    echo "✅ Push réussi!"
else
    echo "❌ Push échoué. Vérifiez la connexion."
fi
