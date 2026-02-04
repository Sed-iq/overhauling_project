# Deployment Troubleshooting Guide

## Common Render Deployment Issues

### 1. Composer Install Fails (Exit Code 2)

**Problem**: `composer install` fails during Docker build

**Solutions**:

#### Option A: Use the Simple Dockerfile
```yaml
# In render.yaml, use:
dockerfilePath: ./Dockerfile.simple
```

#### Option B: Fix Composer Issues
The issue is usually caused by:
- Platform requirements not met
- Missing PHP extensions
- Laravel trying to run scripts during install

**Fixed in our Dockerfiles by**:
- Using `--ignore-platform-reqs` flag
- Using `--no-scripts` flag during install
- Installing all required PHP extensions first

### 2. Missing PHP Extensions

**Error**: `Class "DOMDocument" not found` or similar

**Solution**: Our Dockerfiles install all required extensions:
```dockerfile
RUN docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd xml dom
```

### 3. Permission Issues

**Error**: Permission denied errors for storage or cache

**Solution**: Set proper permissions in Dockerfile:
```dockerfile
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache
```

### 4. Database Issues

**Error**: Database connection fails

**Solutions**:
- Ensure SQLite file exists and has proper permissions
- Check database path in environment variables
- Verify migrations run successfully

### 5. Environment Variables

**Error**: APP_KEY not set or other env issues

**Solution**: Render auto-generates APP_KEY, but you can also:
```yaml
envVars:
  - key: APP_KEY
    generateValue: true
```

## Deployment Options

### Option 1: Simple Dockerfile (Recommended for Render)
```yaml
# render.yaml
dockerfilePath: ./Dockerfile.simple
```

**Pros**:
- Fewer build steps
- Less likely to fail
- Faster builds

### Option 2: Multi-stage Dockerfile
```yaml
# render.yaml  
dockerfilePath: ./Dockerfile.render
```

**Pros**:
- Smaller final image
- Better for production
- Optimized builds

### Option 3: Basic Dockerfile
```yaml
# render.yaml
dockerfilePath: ./Dockerfile
```

**Pros**:
- Full control
- Custom startup scripts
- Advanced configuration

## Testing Locally

### Test Docker Build
```bash
# Test the simple version
docker build -t test-app -f Dockerfile.simple .

# Test with multi-stage
docker build -t test-app -f Dockerfile.render .

# Run locally
docker run -p 8000:80 test-app
```

### Test API
```bash
# Check if app is running
curl http://localhost:8000/api/documentation

# Test login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@greenwood.edu","password":"password123"}'
```

## Render-Specific Issues

### 1. Build Timeout
If build takes too long:
- Use `Dockerfile.simple` for faster builds
- Remove unnecessary dependencies
- Use multi-stage builds

### 2. Memory Issues
If build fails due to memory:
- Upgrade to paid plan
- Optimize Dockerfile
- Remove dev dependencies

### 3. Health Check Fails
If health check at `/api/documentation` fails:
- Check if Apache is serving from correct directory
- Verify routes are working
- Check application logs

## Environment Variables for Render

### Required
```yaml
envVars:
  - key: APP_ENV
    value: production
  - key: APP_KEY
    generateValue: true
  - key: DB_CONNECTION
    value: sqlite
```

### Optional but Recommended
```yaml
  - key: APP_DEBUG
    value: false
  - key: LOG_LEVEL
    value: error
  - key: DB_DATABASE
    value: /var/www/html/database/database.sqlite
```

## Debugging Steps

### 1. Check Build Logs
- Go to Render Dashboard
- Click on your service
- Check "Events" tab for build logs

### 2. Check Runtime Logs
- In service dashboard
- Click "Logs" tab
- Look for PHP/Apache errors

### 3. Test Endpoints
```bash
# Health check
curl https://your-app.onrender.com/api/documentation

# Basic route
curl https://your-app.onrender.com/

# Login test
curl -X POST https://your-app.onrender.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@greenwood.edu","password":"password123"}'
```

## Quick Fixes

### If Build Fails
1. Switch to `Dockerfile.simple`
2. Check composer.json for issues
3. Verify all files are committed to Git

### If App Won't Start
1. Check environment variables
2. Verify database file permissions
3. Check Apache configuration

### If API Returns 500 Errors
1. Check Laravel logs
2. Verify database migrations ran
3. Check file permissions

## Support

If issues persist:
1. Check Render documentation
2. Review build and runtime logs
3. Test locally with Docker
4. Verify all required files are present

## File Checklist

Ensure these files exist:
- ✅ `Dockerfile.simple` (recommended)
- ✅ `render.yaml`
- ✅ `.env.docker`
- ✅ `composer.json` and `composer.lock`
- ✅ All Laravel application files