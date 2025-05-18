#!/bin/bash
set -e

# Lancer Apache en arrière-plan
apache2ctl -D FOREGROUND &

# Lancer le watcher Sass en arrière-plan
npm run watch &

# Attendre que tous les processus se terminent
wait -n
