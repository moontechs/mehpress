# MehPress

MehPress is like WordPress but instead of "Hello World" the first post is "Whatever…".

MehPress is like WordPress but SEO stands for "Sorta, Eventually, Okay".

**JUST ENOUGH!**
* basic templates, slightly broken
* editor crashes only occasionally
* support forum answers in 3–6 months


## Core Business Features

### 🌐 Multi-Blog Management
**Run unlimited blogs from one dashboard** - Each blog gets its own domain, branding, and identity while sharing your content management system.

- **Domain-based routing**: Each blog automatically responds to its configured domain
- **Brand customization**: Unique logos, navigation menus, and footer content per blog
- **Centralized management**: Control all your publications from one admin interface

### 📝 Smart Content Creation
**Two content types optimized for different engagement patterns** - Create both long-form posts and short-form content to maximize audience reach.

- **Posts**: Full articles with rich markdown formatting for in-depth content
- **Shorts**: Quick thoughts and updates for rapid engagement
- **Period-based organization**: Content automatically organized by month/year for easy navigation
- **Link enrichment**: URLs automatically enhanced with previews and metadata

### 🌍 Multi-Language Publishing
**Expand your global reach with intelligent translation** - Create content once and publish in multiple languages with AI assistance.

- **Automatic translation**: AI-powered translation service using OpenRouter
- **Content relationships**: Translated posts maintain connections to originals  
- **Language switching**: Readers can easily switch between available languages
- **SEO optimization**: Each language version gets optimized meta tags

### 🔗 Intelligent Link Management
**Turn every link into an engagement opportunity** - Automatically enhance external links with rich previews and metadata.

- **Automatic metadata extraction**: Links get titles, descriptions, and images automatically
- **Visual previews**: Rich link cards improve reader engagement
- **Background processing**: Link metadata fetched without slowing down publishing
- **Link relationships**: Track which posts reference which external content

### 🎯 Enhanced SEO
**Maximize discoverability with intelligent optimization** - Let AI handle the technical SEO while you focus on creating great content.

- **Smart meta tags**: AI generates optimized titles, descriptions, and keywords
- **Content analysis**: AI analyzes your content for SEO opportunities  
- **Bulk optimization**: Process multiple posts automatically
- **Performance tracking**: Monitor SEO improvements over time

### 🏷️ Flexible Content Organization
**Help readers find exactly what they're looking for** - Organize content with intuitive tagging and filtering systems.

- **Dynamic tagging**: Flexible tag system adapts to your content
- **Tag-based filtering**: Readers can browse content by topic
- **Cross-blog tagging**: Share tag systems across multiple blogs
- **Content discovery**: Related content suggestions based on tags

## Getting Started

### Quick Installation

**Prerequisites**: PHP 8.4+, Composer, Node.js/npm

1. **Set up the project**:
   ```bash
   git clone <repository-url>
   cd mehpress
   cp .env.example .env
   composer install && pnpm install
   ```

2. **Configure your database**:
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   php artisan key:generate
   ```

3. **Start publishing**:
   ```bash
   composer run dev  # Starts server, queue worker, and frontend build
   ```

4. **Access your dashboard**: Visit `/admin` to create your first blog and start publishing.

### Production Deployment

**Docker**: Ready-to-deploy Docker configuration included:
```bash
docker build -t mehpress .
docker run -p 80:80 mehpress
```

**Manual deployment**: Supports any PHP 8.4+ hosting environment with SQLite, MySQL, or PostgreSQL.

## How Your Readers Experience MehPress

### Content Discovery
- **Main feed** (`/`) - Latest content from all types in chronological order
- **Posts feed** (`/posts`) - Long-form articles with month/year navigation  
- **Shorts feed** (`/shorts`) - Quick updates and thoughts
- **Tag browsing** (`/tags`) - Find content by topic or category
- **Individual posts** (`/post/{slug}`) - Full article view with related content
- **Language switching** (`/language/{code}`) - Switch between available languages

### Content Management Interface  
- **Dashboard** (`/admin`) - Overview of all blogs and content performance
- **Blog management** (`/admin/blogs`) - Configure domains, branding, and settings
- **Content creation** (`/admin/posts`) - Write, edit, and publish posts and shorts
- **Translation tools** - Manage multi-language content relationships
- **SEO optimization** - Bulk AI-powered SEO tag generation
- **Link management** - Monitor and enhance external link metadata

## Configuration

### Essential Setup for Business Features

**AI-Powered Features**: Set your OpenRouter API key to enable automatic translation and SEO optimization:
```env
OPENROUTER_API_KEY=your_key_here
```

**Multi-Blog Setup**: Configure your blogs with:
- **Domain mapping**: Each blog responds to its own domain or subdomain
- **Brand identity**: Upload SVG logos and customize color schemes  
- **Navigation menus**: Create custom navigation for each publication
- **Footer content**: Add copyright, contact, and social media links
- **Language support**: Configure available languages and default language per blog

### Available Management Commands

**Content Enhancement**:
```bash
php artisan app:links:fetch-metadata    # Enhance all links with rich previews
php artisan app:generate-seo-ai         # Generate AI-powered SEO tags for posts  
php artisan app:translate-posts         # Translate posts to additional languages
```

**Bulk Operations**: 
Process content in bulk through the admin interface with AI assistance for translation and SEO optimization.

## License

GNU AGPLv3 License
