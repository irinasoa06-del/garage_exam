FROM node:20-alpine

WORKDIR /app

# Copier les fichiers package
COPY vue-project/package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste des fichiers
COPY vue-project/ ./

# Exposer le port
EXPOSE 5173

# Commande par défaut
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
