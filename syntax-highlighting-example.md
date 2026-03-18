# Syntax Highlighting Showcase

Paste everything below into your post editor (Content markdown).

Enable **Syntax highlighting** for the post in the admin UI.

---

## PHP

```php
<?php

declare(strict_types=1);

final class Demo
{
    public function fibonacci(int $n): int
    {
        if ($n <= 1) {
            return $n;
        }

        $a = 0;
        $b = 1;

        for ($i = 2; $i <= $n; $i++) {
            $tmp = $a + $b;
            $a = $b;
            $b = $tmp;
        }

        return $b;
    }
}
```

## JavaScript

```js
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

async function retry(fn, attempts = 3) {
  let lastErr;
  for (let i = 0; i < attempts; i++) {
    try {
      return await fn();
    } catch (e) {
      lastErr = e;
      await sleep(250);
    }
  }
  throw lastErr;
}
```

## TypeScript

```ts
type ApiResponse<T> = {
  ok: boolean;
  data: T;
};

async function getJson<T>(url: string): Promise<ApiResponse<T>> {
  const res = await fetch(url);
  return res.json();
}
```

## Bash

```bash
#!/usr/bin/env bash

set -euo pipefail

echo "Compiling..."
npm run build
echo "Done."
```

## SQL

```sql
SELECT
  p.id,
  p.title,
  p.published_at
FROM posts p
WHERE p.published_at IS NOT NULL
  AND p.archived_at IS NULL
ORDER BY p.published_at DESC
LIMIT 10;
```

## JSON

```json
{
  "app": "makogai.me",
  "features": ["portfolio", "blog", "media-library", "syntax-highlighting"],
  "syntax_highlighting_enabled": true
}
```

## HTML

```html
<section class="card">
  <h2>Hello</h2>
  <p>Prism should highlight this block.</p>
</section>
```

## CSS

```css
.card {
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 16px;
  padding: 16px;
}
```

## Diff

```diff
- old behavior
+ new behavior
@@
   console.log("done");
```

## Markdown inside code (to test escaping)

```md
### Code fences

Use triple backticks:

```js
console.log("hi");
```
```

