#!/bin/bash

# Build script for School Management API

echo "🚀 Building School Management API Docker image..."

# Build the Docker image
docker build -t school-management-api:latest -f Dockerfile.render .

if [ $? -eq 0 ]; then
    echo "✅ Build successful!"
    echo ""
    echo "To run locally:"
    echo "docker run -p 8000:80 school-management-api:latest"
    echo ""
    echo "Then visit: http://localhost:8000/api/documentation"
else
    echo "❌ Build failed!"
    exit 1
fi