#!/bin/bash

echo "🧪 Testing Docker builds..."

# Test simple Dockerfile (recommended for Render)
echo "Testing Dockerfile.simple..."
if docker build -t test-simple -f Dockerfile.simple . --no-cache; then
    echo "✅ Dockerfile.simple builds successfully"
    docker rmi test-simple
else
    echo "❌ Dockerfile.simple failed"
    exit 1
fi

# Test render Dockerfile
echo "Testing Dockerfile.render..."
if docker build -t test-render -f Dockerfile.render . --no-cache; then
    echo "✅ Dockerfile.render builds successfully"
    docker rmi test-render
else
    echo "❌ Dockerfile.render failed"
    exit 1
fi

# Test main Dockerfile
echo "Testing Dockerfile..."
if docker build -t test-main -f Dockerfile . --no-cache; then
    echo "✅ Dockerfile builds successfully"
    docker rmi test-main
else
    echo "❌ Dockerfile failed"
    exit 1
fi

echo "🎉 All Docker builds successful!"