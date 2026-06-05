# Home Dashboard Jordan Map and Sidebar Icons Design

## Scope

Update the `/home` dashboard UI only. Do not add backend queries, geocoding, weather data, or map libraries.

## Recent Activity

Remove sample rows that are not clearly dental-lab activity. Keep dental-lab sample rows such as new cases and clinic payments.

## Jordan Map

Replace the dashboard map placeholder with a static inline SVG map of Jordan.

- Show city markers with static sample values.
- Use varied marker colors to make the map visually useful.
- Do not show a legend.
- Keep the existing dashboard card layout.
- Make the map clickable.
- Open a modal with a larger copy of the same Jordan map for easier reading.

## Sidebar Icons

Replace sidebar icon-font markup with inline SVG icons.

- Use clear icons that match each menu item.
- Replace the operations dashboard Google font icon so it cannot flash as text before loading.
- Do not load the Google Material Symbols stylesheet.
- Preserve routes, permissions, active states, collapse behavior, and menu labels.

## Verification

- Add focused markup tests for the Jordan map modal, dental-lab-only activity samples, and the removal of icon-font markup from the sidebar.
- Run the dashboard feature test.
- Render `/home` and verify the map modal opens without console errors.
