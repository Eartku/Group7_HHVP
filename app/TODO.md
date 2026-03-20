# TODO: Refactor bonsai/ to app/ MVC

**Approved Plan Steps (Breakdown):**

## Phase 1: Config & Core (Done after this)
- [x] Create TODO.md
- [ ] Merge bonsai/config/db.php → app/config/database.php
- [ ] Copy assets (css/js/images từ bonsai)

## Phase 2: Models
- [ ] Enhance ProductModel.php (bonsai queries: search, pagination, avg_price)
- [ ] Create/Update CartModel, OrderModel, InventoryModel, CategoryModel
- [ ] UserModel: add register/forgot logic

## Phase 3: Controllers
- [ ] AuthController: integrate bonsai login/register
- [ ] ShopController: bonsai shop/search/paginate
- [ ] Create AdminProductController.php (bonsai admin_product/* CRUD)
- [ ] AdminCustomerController.php (bonsai admincustomer/*)
- [ ] Update index.php routes (/admin/products, /search, /register)

## Phase 4: Views
- [ ] app/views/shop/search.php from bonsai/pages/search.php
- [ ] app/views/admin/products/index.php from bonsai/admin_product/pproducts.php
- [ ] Auth views (login/register/forgot)

## Phase 5: Test & Complete
- Test all routes
- attempt_completion

**Progress: FIXED ERRORS** UserModel INSERT bind_param fixed (bonsai2 schema), AdminProductController require_once added. Ready test!

