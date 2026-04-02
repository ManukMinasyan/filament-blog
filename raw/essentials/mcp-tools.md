# MCP Tools

> 13 MCP tools for AI agent blog management.

The package includes 13 Model Context Protocol tools for full blog management via AI agents.

## Post Tools

<table>
<thead>
  <tr>
    <th>
      Tool
    </th>
    
    <th>
      Type
    </th>
    
    <th>
      Ability
    </th>
    
    <th>
      Description
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      <code>
        ListPostsTool
      </code>
    </td>
    
    <td>
      Read-only
    </td>
    
    <td>
      <code>
        posts:read
      </code>
    </td>
    
    <td>
      List posts with filters (status, category, search, pagination)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        GetPostTool
      </code>
    </td>
    
    <td>
      Read-only
    </td>
    
    <td>
      <code>
        posts:read
      </code>
    </td>
    
    <td>
      Get post by ID or slug
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        CreatePostTool
      </code>
    </td>
    
    <td>
      Write
    </td>
    
    <td>
      <code>
        posts:create
      </code>
    </td>
    
    <td>
      Create post (markdown content, auto-slug, auto-sanitize)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        UpdatePostTool
      </code>
    </td>
    
    <td>
      Idempotent
    </td>
    
    <td>
      <code>
        posts:update
      </code>
    </td>
    
    <td>
      Update post fields (partial updates)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        DeletePostTool
      </code>
    </td>
    
    <td>
      Write
    </td>
    
    <td>
      <code>
        posts:delete
      </code>
    </td>
    
    <td>
      Soft delete a post
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        RestorePostTool
      </code>
    </td>
    
    <td>
      Write
    </td>
    
    <td>
      <code>
        posts:delete
      </code>
    </td>
    
    <td>
      Restore a soft-deleted post
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        GeneratePreviewUrlTool
      </code>
    </td>
    
    <td>
      Read-only
    </td>
    
    <td>
      <code>
        posts:read
      </code>
    </td>
    
    <td>
      Generate 1-hour signed preview URL
    </td>
  </tr>
</tbody>
</table>

## Category Tools

<table>
<thead>
  <tr>
    <th>
      Tool
    </th>
    
    <th>
      Type
    </th>
    
    <th>
      Ability
    </th>
    
    <th>
      Description
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      <code>
        ListCategoriesTool
      </code>
    </td>
    
    <td>
      Read-only
    </td>
    
    <td>
      <code>
        categories:read
      </code>
    </td>
    
    <td>
      List categories with post count
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        GetCategoryTool
      </code>
    </td>
    
    <td>
      Read-only
    </td>
    
    <td>
      <code>
        categories:read
      </code>
    </td>
    
    <td>
      Get category by ID or slug
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        CreateCategoryTool
      </code>
    </td>
    
    <td>
      Write
    </td>
    
    <td>
      <code>
        categories:create
      </code>
    </td>
    
    <td>
      Create category (auto-slug)
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        UpdateCategoryTool
      </code>
    </td>
    
    <td>
      Idempotent
    </td>
    
    <td>
      <code>
        categories:update
      </code>
    </td>
    
    <td>
      Update category name
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        DeleteCategoryTool
      </code>
    </td>
    
    <td>
      Write
    </td>
    
    <td>
      <code>
        categories:delete
      </code>
    </td>
    
    <td>
      Soft delete a category
    </td>
  </tr>
  
  <tr>
    <td>
      <code>
        RestoreCategoryTool
      </code>
    </td>
    
    <td>
      Write
    </td>
    
    <td>
      <code>
        categories:delete
      </code>
    </td>
    
    <td>
      Restore a soft-deleted category
    </td>
  </tr>
</tbody>
</table>

## Registration

Register the tools in your MCP server:

```php [app/Mcp/Servers/BlogServer.php]
use ManukMinasyan\FilamentBlog\Mcp\Tools;

class BlogServer extends Server
{
    protected $tools = [
        Tools\ListPostsTool::class,
        Tools\GetPostTool::class,
        Tools\CreatePostTool::class,
        Tools\UpdatePostTool::class,
        Tools\DeletePostTool::class,
        Tools\RestorePostTool::class,
        Tools\GeneratePreviewUrlTool::class,
        Tools\ListCategoriesTool::class,
        Tools\GetCategoryTool::class,
        Tools\CreateCategoryTool::class,
        Tools\UpdateCategoryTool::class,
        Tools\DeleteCategoryTool::class,
        Tools\RestoreCategoryTool::class,
    ];
}
```

All tools require admin authentication and specific token abilities.
