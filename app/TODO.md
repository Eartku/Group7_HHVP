# CSS Loading Fix Plan

## Steps:
- [x] 1. Update config/app.php with CSS_URL usage example
- [x] 2. Fix views/layouts/partials/head.php - standardize all CSS to BASE_URL
- [x] 3. Fix views/layouts/main.php - replace loader.php with head.php include
- [x] 4. Create views/layouts/partials/loader.php as alias/redirect if needed
- [ ] 5. Update views/guest/index.php to use layout (remove inline head)
- [ ] 6. Update views/home/index.php similarly
- [x] 7. Modify core/Controller.php view() to use layouts/main.php by default
- [ ] 8. Test http://localhost/app/ CSS loads
- [ ] 9. Complete

Current progress: Layout system implemented with fixed CSS paths. Updating views to content-only.

