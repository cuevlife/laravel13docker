import os
import re

MAPPING = {
    "alert-triangle": "bi-exclamation-triangle",
    "trash-2": "bi-trash-fill",
    "user": "bi-person-fill",
    "paint-brush": "bi-palette-fill",
    "shield": "bi-shield-fill",
    "x": "bi-x-lg",
    "plus": "bi-plus-lg",
    "file-json": "bi-filetype-json",
    "store": "bi-shop",
    "settings-2": "bi-gear-wide-connected",
    "layout-template": "bi-layout-text-window",
    "chevron-down": "bi-chevron-down",
    "arrow-left": "bi-arrow-left",
    "layout": "bi-layout-sidebar",
    "code": "bi-code",
    "sparkles": "bi-stars",
    "loader-2": "bi-arrow-repeat",
    "save": "bi-floppy-fill",
    "briefcase-business": "bi-briefcase-fill",
    "edit-3": "bi-pencil-square",
    "inbox": "bi-inbox-fill",
    "scan-line": "bi-qr-code-scan",
    "shield-check": "bi-shield-check",
    "download": "bi-download",
    "eye": "bi-eye-fill",
    "receipt": "bi-receipt",
    "image-plus": "bi-image",
    "image": "bi-image",
    "maximize-2": "bi-arrows-fullscreen",
    "search": "bi-search",
    "sliders-horizontal": "bi-sliders",
    "filter-x": "bi-funnel-fill",
    "search-check": "bi-search",
    "bot": "bi-robot",
    "cpu": "bi-cpu",
    "coins": "bi-coin",
    "badge-dollar-sign": "bi-cash-coin",
    "refresh-cw": "bi-arrow-repeat",
    "arrow-right": "bi-arrow-right",
}

FILES = [
    "admin/partials/project-list.blade.php",
    "admin/partials/slip-table.blade.php",
    "layouts/parts/header-token.blade.php",
    "main/billing.blade.php",
    "main/dashboard.blade.php",
    "main/exports.blade.php",
    "main/slip-edit.blade.php",
    "main/slip.blade.php",
    "main/stores.blade.php",
    "main/template-edit.blade.php",
    "main/templates.blade.php",
    "profile/edit.blade.php",
    "profile/partials/delete-user-form.blade.php"
]

ROOT_DIR = "SmartBill/resources/views/"

def process_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Regex for finding tags with data-lucide
    pattern = re.compile(r'(<([a-z0-9]+)\b[^>]*data-lucide=([\'"])([^\'"]+)\3[^>]*>)', re.IGNORECASE)

    def replace_tag(match):
        full_tag = match.group(1)
        tag_name = match.group(2)
        lucide_quote = match.group(3)
        icon_name = match.group(4)

        if icon_name not in MAPPING:
            print(f"Warning: Icon '{icon_name}' not found in mapping for file {file_path}")
            bi_icon = f"bi-unknown-{icon_name}"
        else:
            bi_icon = MAPPING[icon_name]

        # Extract existing class attribute
        class_match = re.search(r'class=([\'"])([^\'"]*)\1', full_tag)
        
        if class_match:
            class_quote = class_match.group(1)
            existing_class = class_match.group(2)
            # Remove existing bi classes if any to avoid duplicates
            clean_class = re.sub(r'\bbi bi-[a-z0-9-]+\b', '', existing_class).strip()
            new_class = f"bi {bi_icon} {clean_class}".strip()
            
            # Replace old class with new class
            new_tag = re.sub(r'class=([\'"])([^\'"]*)\1', f'class={class_quote}{new_class}{class_quote}', full_tag)
            # Remove data-lucide
            new_tag = re.sub(r'\s*data-lucide=([\'"])[^\'"]*\1', '', new_tag)
        else:
            # Add class and remove data-lucide
            new_tag = re.sub(r'\s*data-lucide=([\'"])[^\'"]*\1', f' class={lucide_quote}bi {bi_icon}{lucide_quote}', full_tag)

        return new_tag

    new_content = pattern.sub(replace_tag, content)

    if content != new_content:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated: {file_path}")
    else:
        print(f"No changes: {file_path}")

for f in FILES:
    full_path = os.path.join(ROOT_DIR, f)
    if os.path.exists(full_path):
        process_file(full_path)
    else:
        print(f"File not found: {full_path}")
