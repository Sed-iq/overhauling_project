# School Management REST API

A comprehensive School Management REST API built with Laravel for SaaS platforms. Each school operates independently within the same application with proper tenant isolation.

## Features

### Core Requirements ✅

- **Authentication & Authorization**: Laravel Sanctum with role-based access control
- **Multi-Tenancy**: Single database with school-based tenant isolation
- **Database Design**: Comprehensive migrations with foreign keys, indexes, and soft deletes
- **REST API Endpoints**: Full CRUD operations for students and teacher assignments
- **Business Rules**: Enforced constraints and validations
- **Performance**: Pagination, eager loading, and query optimization
- **Security**: Mass assignment protection, input validation, and tenant isolation

### User Roles

- **School Admin**: Manages all school data, creates students, assigns teachers
- **Teacher**: Can access assigned classes and manage students in those classes
- **Student**: Can only access their own profile

## API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/profile` - Get user profile

### Students
- `GET /api/students` - List students (paginated, with search and filters)
- `POST /api/students` - Create new student
- `GET /api/students/{id}` - Get student details
- `PUT /api/students/{id}` - Update student
- `DELETE /api/students/{id}` - Delete student (soft delete)
- `GET /api/students/me` - Get own profile (students only)

### Teachers
- `POST /api/teachers/{teacher_id}/assign-class` - Assign teacher to class
- `GET /api/teachers/{teacher_id}/classes` - Get teacher's assigned classes

## Database Schema

### Tables
- `schools` - School information
- `users` - User accounts (school_admin, teacher, student)
- `classes` - Class information
- `students` - Student profiles with soft deletes
- `teachers` - Teacher profiles
- `teacher_class_assignments` - Teacher-class relationships
- `personal_access_tokens` - Sanctum tokens

### Key Relationships
- Users belong to schools (tenant isolation)
- Students belong to one class
- Teachers can teach multiple classes
- All data is scoped by school_id

## Setup Instructions

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js (optional, for frontend)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd school-management-api
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   Edit `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=school_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

### Test Data

The seeder creates sample data:

**School 1: Greenwood Elementary**
- Admin: `admin@greenwood.edu` / `password123`
- Teacher: `mary@greenwood.edu` / `password123`
- Student: `alice@student.greenwood.edu` / `password123`

**School 2: Riverside High School**
- Admin: `admin@riverside.edu` / `password123`
- Teacher: `bob@riverside.edu` / `password123`
- Student: `charlie@student.riverside.edu` / `password123`

## API Usage Examples

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@greenwood.edu",
    "password": "password123"
  }'
```

### Create Student (as School Admin)
```bash
curl -X POST http://localhost:8000/api/students \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@student.greenwood.edu",
    "password": "password123",
    "class_id": 1,
    "student_id": "S003",
    "date_of_birth": "2015-06-15",
    "guardian_name": "Jane Doe",
    "guardian_phone": "+1-555-3333",
    "address": "123 Student St"
  }'
```

### List Students with Pagination
```bash
curl -X GET "http://localhost:8000/api/students?page=1&search=john" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Assign Teacher to Class
```bash
curl -X POST http://localhost:8000/api/teachers/1/assign-class \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "class_id": 1,
    "subject": "English"
  }'
```

## Testing

Run the test suite:
```bash
php artisan test
```

Run specific test:
```bash
php artisan test --filter StudentApiTest
```

## Architecture Decisions

### Multi-Tenancy Implementation
- **Single Database**: All schools share the same database
- **Tenant Isolation**: Middleware automatically scopes queries by school_id
- **Global Scopes**: Applied to models to ensure data isolation
- **Validation**: Email uniqueness scoped to school level

### Security Measures
- **Laravel Sanctum**: Token-based authentication
- **Role Middleware**: Route-level role checking
- **Policies**: Model-level authorization
- **Mass Assignment Protection**: Fillable properties defined
- **Input Validation**: Form Request classes
- **Password Hashing**: Bcrypt with configurable rounds

### Performance Optimizations
- **Eager Loading**: Prevents N+1 queries
- **Pagination**: 15 items per page by default
- **Database Indexes**: On foreign keys and search fields
- **Query Scoping**: Automatic tenant filtering

### Business Rules Enforced
- Students can only belong to one class
- Teachers can teach multiple classes
- Email uniqueness within school boundaries
- Soft deletes for students (data retention)
- Proper foreign key constraints

## Assumptions Made

1. **Single Application Instance**: All schools use the same application deployment
2. **Shared User Roles**: Role definitions are consistent across all schools
3. **Class Capacity**: Not enforced at database level (business logic only)
4. **Student IDs**: Must be unique within each school
5. **Teacher Specialization**: Single subject specialization per teacher
6. **Soft Deletes**: Only applied to students for data retention
7. **Authentication**: Token-based (suitable for API-first architecture)

## Deployment

### Docker Deployment (Render, Railway, etc.)

The project includes Docker configuration for easy deployment to cloud platforms:

**Quick Deploy to Render:**
1. Push code to GitHub
2. Connect repository to [Render](https://render.com)
3. Render auto-detects `render.yaml` and deploys

**Files included:**
- `Dockerfile.render` - Optimized production Dockerfile
- `render.yaml` - Render service configuration
- `docker-compose.yml` - Local development
- `DEPLOYMENT.md` - Detailed deployment guide

**Test locally:**
```bash
./scripts/build.sh
docker run -p 8000:80 school-management-api:latest
```

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed instructions.

## Future Enhancements

- **Caching**: Redis integration for school details and class lists
- **File Uploads**: Student photos and documents
- **Audit Logging**: Track all data changes
- **Advanced Reporting**: Analytics and insights
- **Notification System**: Email/SMS notifications
- **Mobile API**: Optimized endpoints for mobile apps
- **Multi-language Support**: Internationalization
- **Advanced Search**: Full-text search capabilities

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Run the test suite
6. Submit a pull request

## License

This project is licensed under the MIT License.