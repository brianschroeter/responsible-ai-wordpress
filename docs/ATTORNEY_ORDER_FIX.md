# Attorney Selection Order Issue & Solution

## Problem
When using the "Select" mode for attorney display in the sidebar, the attorneys aren't appearing in the order selected in the WordPress admin.

## Root Cause
ACF Relationship fields save selected posts in the order they're arranged in the admin interface. The code correctly iterates through the array in order, BUT there's a critical issue:

**The ACF relationship field saves post IDs in a serialized array.** When you select "Lee, Andre, Wardell" in that order, ACF saves them as `[127, 129, 132]`. The `get_field()` function returns them in the EXACT order they were saved.

## Current Code (Already Correct)
```php
// In template-tags.php lines 556-571
elseif ($sidebar_mode === 'select' && function_exists('get_field')) {
    // Use specifically selected attorneys
    $selected = get_field('sidebar_attorneys_selected', $front_page_id);
    if ($selected && is_array($selected)) {
        foreach ($selected as $attorney_post) {
            $att_id = $attorney_post->ID;
            $photo = get_field('photo', $att_id);
            $attorneys[] = array(
                'name' => get_the_title($att_id),
                'title' => get_field('attorney_title', $att_id) ?: '',
                'credentials' => get_field('credentials', $att_id) ?: '',
                'photo' => $photo ? $photo['url'] : '',
                'link' => get_permalink($att_id),
            );
        }
    }
}
```

This code **DOES preserve order** - it iterates through `$selected` in the exact order returned by `get_field()`.

## Solution: How to Set Order in WordPress Admin

### Step 1: Go to Front Page Settings
1. Navigate to **Pages → Front Page → Edit**
2. Scroll to **Homepage - Why Choose Us → Sidebar Configuration** tab
3. Set **Attorney Display Mode** to "Select"

### Step 2: Select Attorneys in Desired Order
1. Click the **"+ Add Attorney"** button
2. **IMPORTANT**: Add them in the EXACT order you want:
   - Click Lee Stein first
   - Then click Andre Sailers
   - Then click Wardell Huff

### Step 3: Reorder if Needed
- You can **drag and drop** to reorder them in the relationship field
- The order shown in the admin field IS the order they'll appear on the frontend

### Step 4: Update and Clear Cache
1. Click **Update** to save
2. Clear any WordPress cache
3. Refresh the frontend page

## Verification

### Check Order in Database
```bash
# Get the front page ID
docker-compose run --rm wp-cli wp post list --post_type=page --name=front-page --fields=ID --format=ids

# View the relationship field data (replace PAGE_ID)
docker-compose run --rm wp-cli wp post meta get PAGE_ID sidebar_attorneys_selected
```

This should show the array of attorney IDs in the order: `[127, 129, 132]` (Lee, Andre, Wardell).

### Check Frontend Output
Visit http://localhost:8088/ and inspect the "Why Choose Us" section sidebar. The attorneys should appear in the order:
1. Lee Stein, Esq. - Admitted in FL - 20+ Years Experience
2. Andre Sailers, Esq. - Admitted in GA - 32+ Years Experience
3. Wardell Huff, Esq. - Admitted in DC, NJ, NY, MD - 20+ Years

## Why This Happens

ACF Relationship fields work like this:
1. **Admin UI Order = Database Order = Frontend Order**
2. The drag-and-drop interface in the admin saves the exact order
3. `get_field()` returns posts in that saved order
4. Our `foreach` loop preserves that order

## If Order Is Still Wrong

### Option 1: Re-select in Correct Order
- Remove all attorneys from the relationship field
- Add them back in the correct order: Lee → Andre → Wardell
- Save

### Option 2: Use Auto Mode Instead
If you want consistent ordering based on menu_order:
1. Set **Attorney Display Mode** to "Auto"
2. Set **Number of Attorneys** to 3
3. The system will automatically show the first 3 attorneys ordered by menu_order (1, 2, 3)

This ensures:
- Lee Stein (menu_order=1) appears first
- Andre Sailers (menu_order=2) appears second
- Wardell Huff (menu_order=3) appears third

## Attorney Posts Updated

The three attorney posts have been updated with correct data:

| ID  | Attorney               | Menu Order | Credentials                                |
|-----|------------------------|------------|--------------------------------------------|
| 127 | Lee Stein, Esq.        | 1          | Admitted in FL - 20+ Years Experience      |
| 129 | Andre Sailers, Esq.    | 2          | Admitted in GA - 32+ Years Experience      |
| 132 | Wardell Huff, Esq.     | 3          | Admitted in DC, NJ, NY, MD - 20+ Years     |

## Commands Used to Update Posts

```bash
# Lee Stein
docker-compose run --rm wp-cli wp post update 127 --menu_order=1
docker-compose run --rm wp-cli wp post meta update 127 _attorney_title 'Managing Partner'
docker-compose run --rm wp-cli wp post meta update 127 _credentials 'Admitted in FL - 20+ Years Experience'

# Andre Sailers
docker-compose run --rm wp-cli wp post update 129 --menu_order=2
docker-compose run --rm wp-cli wp post meta update 129 _attorney_title 'Senior Partner'
docker-compose run --rm wp-cli wp post meta update 129 _credentials 'Admitted in GA - 32+ Years Experience'

# Wardell Huff
docker-compose run --rm wp-cli wp post update 132 --menu_order=3
docker-compose run --rm wp-cli wp post meta update 132 _attorney_title 'Partner'
docker-compose run --rm wp-cli wp post meta update 132 _credentials 'Admitted in DC, NJ, NY, MD - 20+ Years'
```

## Recommended: Use Auto Mode

For the most reliable ordering, I recommend using **Auto Mode**:
- It always respects menu_order
- No manual selection needed
- Consistent ordering
- Matches the live site structure

This is what the live site appears to use (first 3 attorneys by some consistent ordering).
