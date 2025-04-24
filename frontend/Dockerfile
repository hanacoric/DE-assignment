# Use a lightweight Node.js image
FROM node:lts-alpine as builder

# Set working directory
WORKDIR /app

# Copy package.json and package-lock.json (or yarn.lock)
COPY frontend/package*.json ./

# Install frontend dependencies
RUN npm install # or yarn install

# Copy the rest of the frontend files
COPY frontend/. .

# Build the Vue.js application for production
RUN npm run build # or yarn build

# --- Production Stage ---
FROM nginx:alpine

# Copy the built frontend assets from the builder stage
COPY --from=builder /app/dist /usr/share/nginx/html

# Expose port 80 for Nginx
EXPOSE 80

# Optionally, you can add a custom nginx configuration here
# COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

# Start Nginx
CMD ["nginx", "-g", "daemon off;"]
