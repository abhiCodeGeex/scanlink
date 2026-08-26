import sys
from playwright.sync_api import sync_playwright

out = sys.argv[1]
css = open(sys.argv[2], encoding="utf-8").read() if len(sys.argv) > 2 else None
clip_arg = sys.argv[3] if len(sys.argv) > 3 else None  # "x,y,w,h" optional

with sync_playwright() as p:
    b = p.chromium.launch()
    page = b.new_page(viewport={"width": 1024, "height": 900})
    page.goto("http://localhost:8000/debug-fb.html", wait_until="networkidle")
    if css:
        page.add_style_tag(content=css)
    if clip_arg:
        x, y, w, h = [int(v) for v in clip_arg.split(",")]
        page.screenshot(path=out, clip={"x": x, "y": y, "width": w, "height": h})
    else:
        page.screenshot(path=out, full_page=True)
    print(page.evaluate("() => document.body.scrollHeight"))
    b.close()
