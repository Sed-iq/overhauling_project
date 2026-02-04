#!/bin/bash

# Deployment preparation script

echo "📦 Preparing for deployment..."

# Check if required files exist
required_files=(
    "Dockerfile.render"
    "render.yaml"
    "docker/apache/000-default.conf"
    "docker/startup.sh"
    ".env.production"
)

for file in "${required_files[@]}"; do
    if [ ! -f "$file" ]; then
        echo "❌ Missing required file: $file"
        exit 1
    fi
done

echo "✅ All required files present"

# Check if .env.production has APP_KEY placeholder
if grep -q "APP_KEY=$" .env.production; then
    echo "⚠️  APP_KEY is empty in .env.production - Render will generate one"
fi

# Validate Docker files
echo "🔍 Validating Dockerfile..."
docker build -t school-management-api:test -f Dockerfile.render . --no-cache

if [ $? -eq 0 ]; then
    echo "✅ Docker build successful!"
    
    # Clean up test image
    docker rmi school-management-api:test
    
    echo ""
    echo "🚀 Ready for deployment!"
    echo ""
    echo "Next steps:"
    echo "1. Push your code to GitHub"
    echo "2. Connect repository to Render"
    echo "3. Render will auto-deploy using render.yaml"
    echo ""
    echo "Or manually configure with:"
    echo "- Dockerfile: ./Dockerfile.render"
    echo "- Health check: /api/documentation"
else
    echo "❌ Docker build failed! Check the logs above."
    exit 1
fi