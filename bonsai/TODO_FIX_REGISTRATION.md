# FIX: User not inserted after register in bonsai/pages/register.php

✅ 1. Analyze files → INSERT query OK, DB schema OK, legacy pages/ handler.

✅ 2. Confirmed bonsai/app MVC files empty (use pages/register.php).

⏳ 3. Add DB error logging to register.php.

⏳ 4. Test register new unique user.

⏳ 5. Improve validation/error msgs.

⏳ 6. Migrate to MVC (optional).

⏳ 7. Full test: register → login → DB verify.

## Next: Edit register.php for debug
