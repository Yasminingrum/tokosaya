<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'path',
        'url',
        'mime_type',
        'size_bytes',
        'alt_text',
        'caption',
        'width',
        'height',
        'uploaded_by',
        'folder',
        'is_public',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    public function scopeDocuments($query)
    {
        return $query->where('mime_type', 'not like', 'image/%')
                    ->where('mime_type', 'not like', 'video/%');
    }

    public function scopeByFolder($query, $folder)
    {
        return $query->where('folder', $folder);
    }

    public function scopeByUploader($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByMimeType($query, $mimeType)
    {
        return $query->where('mime_type', $mimeType);
    }

    public function scopeBySizeRange($query, $minBytes = null, $maxBytes = null)
    {
        if ($minBytes !== null) {
            $query->where('size_bytes', '>=', $minBytes);
        }

        if ($maxBytes !== null) {
            $query->where('size_bytes', '<=', $maxBytes);
        }

        return $query;
    }

    // File type helpers
    public function isImage()
    {
        return Str::startsWith($this->mime_type, 'image/');
    }

    public function isVideo()
    {
        return Str::startsWith($this->mime_type, 'video/');
    }

    public function isDocument()
    {
        return !$this->isImage() && !$this->isVideo();
    }

    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    public function isArchive()
    {
        return in_array($this->mime_type, [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
        ]);
    }

    // File info helpers
    public function getFileExtensionAttribute()
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    public function getFileSizeFormattedAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->size_bytes;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getFullUrlAttribute()
    {
        if (Str::startsWith($this->url, ['http://', 'https://'])) {
            return $this->url;
        }

        return asset('storage/' . $this->url);
    }

    public function getThumbnailUrlAttribute($size = 300)
    {
        if (!$this->isImage()) {
            return $this->getDefaultThumbnail();
        }

        // In a real application, you might use image processing service
        return $this->full_url;
    }

    public function getDefaultThumbnail()
    {
        if ($this->isVideo()) {
            return asset('images/thumbnails/video.png');
        }

        if ($this->isPdf()) {
            return asset('images/thumbnails/pdf.png');
        }

        if ($this->isArchive()) {
            return asset('images/thumbnails/archive.png');
        }

        return match($this->file_extension) {
            'doc', 'docx' => asset('images/thumbnails/word.png'),
            'xls', 'xlsx' => asset('images/thumbnails/excel.png'),
            'ppt', 'pptx' => asset('images/thumbnails/powerpoint.png'),
            'txt' => asset('images/thumbnails/text.png'),
            default => asset('images/thumbnails/file.png'),
        };
    }

    // Image dimension helpers
    public function getAspectRatioAttribute()
    {
        if (!$this->width || !$this->height) {
            return null;
        }

        return $this->width / $this->height;
    }

    public function isLandscape()
    {
        return $this->aspect_ratio > 1;
    }

    public function isPortrait()
    {
        return $this->aspect_ratio < 1;
    }

    public function isSquare()
    {
        return abs($this->aspect_ratio - 1) < 0.1;
    }

    public function hasMetadata()
    {
        return $this->width && $this->height;
    }

    public function getDimensionsAttribute()
    {
        if (!$this->hasMetadata()) {
            return null;
        }

        return "{$this->width} × {$this->height}";
    }

    // Display helpers
    public function getTypeIconAttribute()
    {
        if ($this->isImage()) {
            return 'image';
        }

        if ($this->isVideo()) {
            return 'video';
        }

        return match($this->file_extension) {
            'pdf' => 'file-text',
            'doc', 'docx' => 'file-text',
            'xls', 'xlsx' => 'file-spreadsheet',
            'ppt', 'pptx' => 'file-presentation',
            'zip', 'rar', '7z' => 'archive',
            'txt' => 'file-text',
            'csv' => 'file-csv',
            default => 'file',
        };
    }

    public function getTypeColorAttribute()
    {
        if ($this->isImage()) {
            return 'success';
        }

        if ($this->isVideo()) {
            return 'primary';
        }

        return match($this->file_extension) {
            'pdf' => 'danger',
            'doc', 'docx' => 'info',
            'xls', 'xlsx' => 'success',
            'ppt', 'pptx' => 'warning',
            'zip', 'rar', '7z' => 'secondary',
            'txt' => 'dark',
            'csv' => 'info',
            default => 'muted',
        };
    }

    public function getAltTextOrNameAttribute()
    {
        return $this->alt_text ?: $this->original_name;
    }

    public function getDisplayNameAttribute()
    {
        return $this->original_name ?: $this->filename;
    }

    // Status helpers
    public function isRecentlyUploaded($days = 7)
    {
        return $this->created_at->isAfter(now()->subDays($days));
    }

    public function isLargeFile($sizeMB = 5)
    {
        return $this->size_bytes > ($sizeMB * 1024 * 1024);
    }

    public function getRelativePathAttribute()
    {
        return str_replace(storage_path('app/public/'), '', $this->path);
    }

    // Usage tracking methods (with safe model checks)
    public function canBeDeleted()
    {
        return $this->getUsageCount() === 0;
    }

    public function isUsedInProducts()
    {
        $count = 0;

        if (class_exists('App\\Models\\ProductImage')) {
            $count += \App\Models\ProductImage::where('image_url', $this->path)->count();
        }

        if (class_exists('App\\Models\\ProductVariant')) {
            $count += \App\Models\ProductVariant::where('image', $this->path)->count();
        }

        return $count > 0;
    }

    public function isUsedInPages()
    {
        if (!class_exists('App\\Models\\Page')) {
            return false;
        }

        return \App\Models\Page::where('featured_image', $this->path)->exists() ||
               \App\Models\Page::where('content', 'like', '%' . $this->path . '%')->exists();
    }

    public function isUsedInBanners()
    {
        if (!class_exists('App\\Models\\Banner')) {
            return false;
        }

        return \App\Models\Banner::where('image', $this->path)->exists() ||
               \App\Models\Banner::where('mobile_image', $this->path)->exists();
    }

    public function isUsedInBrands()
    {
        if (!class_exists('App\\Models\\Brand')) {
            return false;
        }

        return \App\Models\Brand::where('logo', $this->path)->exists();
    }

    public function getUsageCount()
    {
        $count = 0;

        // Count usage in product images (with safe check)
        if (class_exists('App\\Models\\ProductImage')) {
            $count += \App\Models\ProductImage::where('image_url', $this->path)->count();
        }

        // Count usage in product variants (with safe check)
        if (class_exists('App\\Models\\ProductVariant')) {
            $count += \App\Models\ProductVariant::where('image', $this->path)->count();
        }

        // Count usage in pages (with safe check)
        if (class_exists('App\\Models\\Page')) {
            $count += \App\Models\Page::where('featured_image', $this->path)->count();
            $count += \App\Models\Page::where('content', 'like', '%' . $this->path . '%')->count();
        }

        // Count usage in banners (with safe check)
        if (class_exists('App\\Models\\Banner')) {
            $count += \App\Models\Banner::where('image', $this->path)->count();
            $count += \App\Models\Banner::where('mobile_image', $this->path)->count();
        }

        // Count usage in brands (with safe check)
        if (class_exists('App\\Models\\Brand')) {
            $count += \App\Models\Brand::where('logo', $this->path)->count();
        }

        return $count;
    }

    public function getUsageDetails()
    {
        $usage = [];

        if (class_exists('App\\Models\\ProductImage')) {
            $usage['product_images'] = \App\Models\ProductImage::where('image_url', $this->path)->count();
        }

        if (class_exists('App\\Models\\ProductVariant')) {
            $usage['product_variants'] = \App\Models\ProductVariant::where('image', $this->path)->count();
        }

        if (class_exists('App\\Models\\Page')) {
            $usage['page_featured'] = \App\Models\Page::where('featured_image', $this->path)->count();
            $usage['page_content'] = \App\Models\Page::where('content', 'like', '%' . $this->path . '%')->count();
        }

        if (class_exists('App\\Models\\Banner')) {
            $usage['banners'] = \App\Models\Banner::where('image', $this->path)->orWhere('mobile_image', $this->path)->count();
        }

        if (class_exists('App\\Models\\Brand')) {
            $usage['brands'] = \App\Models\Brand::where('logo', $this->path)->count();
        }

        return array_filter($usage, function ($count) {
            return $count > 0;
        });
    }

    public function getUsageLocations()
    {
        $locations = [];

        // Product images
        $productImages = ProductImage::where('image_url', $this->path)->with('product')->get();
        foreach ($productImages as $productImage) {
            $locations[] = [
                'type' => 'Product Image',
                'name' => $productImage->product->name,
                'url' => route('admin.products.show', $productImage->product->id),
            ];
        }

        // Product variants
        $productVariants = ProductVariant::where('image', $this->path)->with('product')->get();
        foreach ($productVariants as $variant) {
            $locations[] = [
                'type' => 'Product Variant',
                'name' => $variant->product->name . ' - ' . $variant->display_name,
                'url' => route('admin.products.show', $variant->product->id),
            ];
        }

        // Pages
        $pages = Page::where('featured_image', $this->path)
                    ->orWhere('content', 'like', '%' . $this->path . '%')
                    ->get();
        foreach ($pages as $page) {
            $locations[] = [
                'type' => 'Page',
                'name' => $page->title,
                'url' => route('admin.pages.show', $page->id),
            ];
        }

        // Banners
        $banners = Banner::where('image', $this->path)
                        ->orWhere('mobile_image', $this->path)
                        ->get();
        foreach ($banners as $banner) {
            $locations[] = [
                'type' => 'Banner',
                'name' => $banner->title,
                'url' => route('admin.banners.show', $banner->id),
            ];
        }

        // Brands
        $brands = Brand::where('logo', $this->path)->get();
        foreach ($brands as $brand) {
            $locations[] = [
                'type' => 'Brand Logo',
                'name' => $brand->name,
                'url' => route('admin.brands.show', $brand->id),
            ];
        }

        return $locations;
    }

    // Action methods
    public function move($newFolder)
    {
        $oldFolder = $this->folder;
        $this->update(['folder' => $newFolder]);

        // Log the move action if ActivityLog model exists
        if (class_exists(ActivityLog::class)) {
            ActivityLog::log('moved', "Moved file from {$oldFolder} to {$newFolder}", static::class, $this->id);
        }
    }

    public function makePrivate()
    {
        $this->update(['is_public' => false]);
    }

    public function makePublic()
    {
        $this->update(['is_public' => true]);
    }

    public function updateMetadata($altText = null, $caption = null)
    {
        $this->update([
            'alt_text' => $altText,
            'caption' => $caption,
        ]);
    }

    // Static methods
    public static function findByPath($path)
    {
        return static::where('path', $path)->first();
    }

    public static function findByUrl($url)
    {
        return static::where('url', $url)->first();
    }

    public static function findByFilename($filename)
    {
        return static::where('filename', $filename)->first();
    }

    public static function getByFolder($folder)
    {
        return static::where('folder', $folder)->orderBy('created_at', 'desc')->get();
    }

    public static function getRecentUploads($limit = 20, $days = 30)
    {
        return static::where('created_at', '>=', now()->subDays($days))
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    public static function getLargestFiles($limit = 10)
    {
        return static::orderBy('size_bytes', 'desc')
                    ->limit($limit)
                    ->get();
    }

    public static function getUnusedFiles($days = 30)
    {
        return static::where('created_at', '<', now()->subDays($days))
                    ->get()
                    ->filter(function ($file) {
                        return $file->getUsageCount() === 0;
                    });
    }

    public static function cleanup($days = 30)
    {
        $unusedFiles = static::getUnusedFiles($days);
        $deletedCount = 0;

        foreach ($unusedFiles as $file) {
            // Delete physical file
            $filePath = storage_path('app/public/' . $file->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete thumbnails if they exist
            $thumbnailBase = storage_path('app/public/thumbnails/');
            $thumbnailPattern = $thumbnailBase . '*/' . $file->filename;
            foreach (glob($thumbnailPattern) as $thumbnailFile) {
                if (file_exists($thumbnailFile)) {
                    unlink($thumbnailFile);
                }
            }

            // Delete database record
            $file->delete();
            $deletedCount++;
        }

        return $deletedCount;
    }

    public static function getTotalSize()
    {
        return static::sum('size_bytes');
    }

    public static function getFormattedTotalSize()
    {
        $totalBytes = static::getTotalSize();
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $totalBytes;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public static function getStatistics()
    {
        return [
            'total_files' => static::count(),
            'total_size' => static::getFormattedTotalSize(),
            'total_images' => static::images()->count(),
            'total_videos' => static::videos()->count(),
            'total_documents' => static::documents()->count(),
            'public_files' => static::where('is_public', true)->count(),
            'recent_uploads' => static::where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    public static function createFromUpload($uploadedFile, $folder = 'uploads', $isPublic = true, $uploadedBy = null)
    {
        $filename = time() . '_' . Str::random(10) . '.' . $uploadedFile->getClientOriginalExtension();
        $path = $uploadedFile->storeAs($folder, $filename, 'public');

        $mediaFile = static::create([
            'filename' => $filename,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'path' => $path,
            'url' => $path,
            'mime_type' => $uploadedFile->getMimeType(),
            'size_bytes' => $uploadedFile->getSize(),
            'uploaded_by' => $uploadedBy ?: optional(auth())->id(),
            'folder' => $folder,
            'is_public' => $isPublic,
        ]);

        // Extract image dimensions if it's an image
        if ($mediaFile->isImage()) {
            $imagePath = storage_path('app/public/' . $path);
            if (file_exists($imagePath)) {
                $imageSize = getimagesize($imagePath);
                if ($imageSize) {
                    $mediaFile->update([
                        'width' => $imageSize[0],
                        'height' => $imageSize[1],
                    ]);
                }
            }
        }

        return $mediaFile;
    }

    // Events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mediaFile) {
            if (empty($mediaFile->folder)) {
                $mediaFile->folder = 'uploads';
            }
        });

        static::deleting(function ($mediaFile) {
            // Delete physical file when model is deleted
            $filePath = storage_path('app/public/' . $mediaFile->path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete thumbnails
            $thumbnailBase = storage_path('app/public/thumbnails/');
            $thumbnailPattern = $thumbnailBase . '*/' . $mediaFile->filename;
            foreach (glob($thumbnailPattern) as $thumbnailFile) {
                if (file_exists($thumbnailFile)) {
                    unlink($thumbnailFile);
                }
            }

            // Log the deletion if ActivityLog exists
            if (class_exists(ActivityLog::class)) {
                ActivityLog::log('deleted', "Deleted media file: {$mediaFile->original_name}", static::class, $mediaFile->id);
            }
        });
    }
}
