# Filament Admin

> Managing blog posts and categories in the Filament admin panel.

## Posts Resource

The PostResource provides a full CRUD interface for blog posts.

### Form Fields

<table>
<thead>
  <tr>
    <th>
      Field
    </th>
    
    <th>
      Type
    </th>
    
    <th>
      Notes
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      Title
    </td>
    
    <td>
      TextInput
    </td>
    
    <td>
      Required, max 255
    </td>
  </tr>
  
  <tr>
    <td>
      Slug
    </td>
    
    <td>
      TextInput
    </td>
    
    <td>
      Auto-generated, unique
    </td>
  </tr>
  
  <tr>
    <td>
      Content
    </td>
    
    <td>
      MarkdownEditor
    </td>
    
    <td>
      Required, full toolbar
    </td>
  </tr>
  
  <tr>
    <td>
      Excerpt
    </td>
    
    <td>
      Textarea
    </td>
    
    <td>
      3 rows
    </td>
  </tr>
  
  <tr>
    <td>
      Status
    </td>
    
    <td>
      Toggle
    </td>
    
    <td>
      Published/Draft
    </td>
  </tr>
  
  <tr>
    <td>
      Published At
    </td>
    
    <td>
      DateTimePicker
    </td>
    
    <td>
      Schedule future posts
    </td>
  </tr>
  
  <tr>
    <td>
      Category
    </td>
    
    <td>
      Select
    </td>
    
    <td>
      Searchable, create inline
    </td>
  </tr>
  
  <tr>
    <td>
      Author
    </td>
    
    <td>
      Select
    </td>
    
    <td>
      Defaults to current user
    </td>
  </tr>
  
  <tr>
    <td>
      Featured Image
    </td>
    
    <td>
      FileUpload
    </td>
    
    <td>
      Image, public disk
    </td>
  </tr>
  
  <tr>
    <td>
      SEO
    </td>
    
    <td>
      SEO Component
    </td>
    
    <td>
      Title + description (collapsible)
    </td>
  </tr>
</tbody>
</table>

### List Tabs

Posts are organized into tabs:

<table>
<thead>
  <tr>
    <th>
      Tab
    </th>
    
    <th>
      Filter
    </th>
  </tr>
</thead>

<tbody>
  <tr>
    <td>
      <strong>
        Drafts
      </strong>
    </td>
    
    <td>
      <code>
        status = draft
      </code>
    </td>
  </tr>
  
  <tr>
    <td>
      <strong>
        Scheduled
      </strong>
    </td>
    
    <td>
      <code>
        status = published
      </code>
      
       AND <code>
        published_at > now
      </code>
    </td>
  </tr>
  
  <tr>
    <td>
      <strong>
        Published
      </strong>
    </td>
    
    <td>
      <code>
        status = published
      </code>
      
       AND <code>
        published_at <= now
      </code>
    </td>
  </tr>
</tbody>
</table>

### Actions

- **View** — Opens post URL (published) or signed preview URL (draft)
- **Edit** — Edit post form
- **Delete / Force Delete / Restore** — Soft delete support

## Categories Resource

Simple CRUD for blog categories with name, slug, and post count.

## Plugin Registration

```php [AppPanelProvider.php]
use ManukMinasyan\FilamentBlog\FilamentBlogPlugin;

$panel->plugins([
    FilamentBlogPlugin::make(),
]);
```

The plugin auto-discovers PostResource and CategoryResource under the "Blog" navigation group.
