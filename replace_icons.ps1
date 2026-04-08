$mapping = @{
    "alert-triangle" = "bi-exclamation-triangle"
    "trash-2" = "bi-trash-fill"
    "user" = "bi-person-fill"
    "paint-brush" = "bi-palette-fill"
    "shield" = "bi-shield-fill"
    "x" = "bi-x-lg"
    "plus" = "bi-plus-lg"
    "file-json" = "bi-filetype-json"
    "store" = "bi-shop"
    "settings-2" = "bi-gear-wide-connected"
    "layout-template" = "bi-layout-text-window"
    "chevron-down" = "bi-chevron-down"
    "arrow-left" = "bi-arrow-left"
    "layout" = "bi-layout-sidebar"
    "code" = "bi-code"
    "sparkles" = "bi-stars"
    "loader-2" = "bi-arrow-repeat"
    "save" = "bi-floppy-fill"
    "briefcase-business" = "bi-briefcase-fill"
    "edit-3" = "bi-pencil-square"
    "inbox" = "bi-inbox-fill"
    "scan-line" = "bi-qr-code-scan"
    "shield-check" = "bi-shield-check"
    "download" = "bi-download"
    "eye" = "bi-eye-fill"
    "receipt" = "bi-receipt"
    "image-plus" = "bi-image"
    "image" = "bi-image"
    "maximize-2" = "bi-arrows-fullscreen"
    "search" = "bi-search"
    "sliders-horizontal" = "bi-sliders"
    "filter-x" = "bi-funnel-fill"
    "search-check" = "bi-search"
    "bot" = "bi-robot"
    "cpu" = "bi-cpu"
    "coins" = "bi-coin"
    "badge-dollar-sign" = "bi-cash-coin"
    "refresh-cw" = "bi-arrow-repeat"
    "arrow-right" = "bi-arrow-right"
}

$files = @(
    "admin\partials\project-list.blade.php",
    "admin\partials\slip-table.blade.php",
    "layouts\parts\header-token.blade.php",
    "main\billing.blade.php",
    "main\dashboard.blade.php",
    "main\exports.blade.php",
    "main\slip-edit.blade.php",
    "main\slip.blade.php",
    "main\stores.blade.php",
    "main\template-edit.blade.php",
    "main\templates.blade.php",
    "profile\edit.blade.php",
    "profile\partials\delete-user-form.blade.php"
)

foreach ($f in $files) {
    $p = "SmartBill\resources\views\$f"
    if (Test-Path $p) {
        $c = Get-Content $p -Raw
        $newContent = [regex]::Replace($c, '(<([a-z0-9]+)\b[^>]*data-lucide=([''"])([^\''"]+)\3[^>]*>)', {
            param($match)
            $fullTag = $match.Value
            $tagName = $match.Groups[2].Value
            $lucideQuote = $match.Groups[3].Value
            $iconName = $match.Groups[4].Value
            $biIcon = $mapping[$iconName]
            if (-not $biIcon) { return $fullTag }

            if ($fullTag -match 'class=([''"])([^\''"]*)\1') {
                $classQuote = $matches[1]
                $existingClass = $matches[2]
                $newClass = "bi $biIcon $existingClass".Trim()
                $newTag = [regex]::Replace($fullTag, 'class=([''"])([^\''"]*)\1', "class=$classQuote$newClass$classQuote")
                $newTag = [regex]::Replace($newTag, '\s*data-lucide=([''"])[^\''"]*\1', "")
                return [regex]::Replace($newTag, '\s+>', ">")
            } else {
                $newTag = [regex]::Replace($fullTag, '\s*data-lucide=([''"])[^\''"]*\1', " class=$lucideQuotebi $biIcon$lucideQuote")
                return $newTag
            }
        })
        if ($c -ne $newContent) {
            Set-Content $p $newContent -NoNewline
            Write-Host "Updated: $p"
        }
    }
}
