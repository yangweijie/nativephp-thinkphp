# 国际化示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的国际化功能，包括语言切换、翻译和本地化。

## 功能

- 多语言支持
- 语言切换
- 文本翻译
- 日期和时间格式化
- 数字和货币格式化
- 本地化设置

## 文件结构

- `app/controller/I18n.php` - 国际化控制器
- `view/i18n/index.html` - 主页面
- `view/i18n/settings.html` - 设置页面
- `public/static/js/app.js` - 前端 JavaScript 代码
- `public/static/css/app.css` - 前端 CSS 样式
- `resources/lang/zh-cn.php` - 中文语言文件
- `resources/lang/en-us.php` - 英文语言文件

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 实现说明

本示例使用 NativePHP for ThinkPHP 的以下功能：

- **Translator**：用于翻译文本
- **LanguageSwitcher**：用于切换语言
- **Settings**：用于保存语言设置

本示例实现了以下功能：

1. **多语言支持**：支持中文、英文等多种语言
2. **语言切换**：提供多种语言切换器样式
3. **文本翻译**：翻译界面文本
4. **日期和时间格式化**：根据语言格式化日期和时间
5. **数字和货币格式化**：根据语言格式化数字和货币
6. **本地化设置**：保存用户的语言偏好

## 代码示例

### 控制器

```php
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Translator;
use Native\ThinkPHP\Facades\LanguageSwitcher;
use Native\ThinkPHP\Facades\Window;
use think\facade\View;

class I18n extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取当前语言
        $locale = Translator::getLocale();
        $localeName = LanguageSwitcher::getLocaleName($locale);
        
        // 获取可用语言列表
        $locales = LanguageSwitcher::getAvailableLocales();
        $localeNames = LanguageSwitcher::getLocaleNames();
        
        // 渲染语言切换器
        $languageSwitcher = LanguageSwitcher::render();
        $languageSwitcherBootstrap = LanguageSwitcher::renderBootstrap();
        $languageSwitcherButtons = LanguageSwitcher::renderButtons();
        $languageSwitcherDropdown = LanguageSwitcher::renderDropdown();
        
        // 传递数据到视图
        return View::fetch('i18n/index', [
            'locale' => $locale,
            'localeName' => $localeName,
            'locales' => $locales,
            'localeNames' => $localeNames,
            'languageSwitcher' => $languageSwitcher,
            'languageSwitcherBootstrap' => $languageSwitcherBootstrap,
            'languageSwitcherButtons' => $languageSwitcherButtons,
            'languageSwitcherDropdown' => $languageSwitcherDropdown,
        ]);
    }
    
    /**
     * 显示设置页面
     *
     * @return \think\Response
     */
    public function settings()
    {
        // 获取当前语言
        $locale = Translator::getLocale();
        $localeName = LanguageSwitcher::getLocaleName($locale);
        
        // 获取可用语言列表
        $locales = LanguageSwitcher::getAvailableLocales();
        $localeNames = LanguageSwitcher::getLocaleNames();
        
        // 渲染语言切换器
        $languageSwitcher = LanguageSwitcher::renderBootstrap();
        
        // 传递数据到视图
        return View::fetch('i18n/settings', [
            'locale' => $locale,
            'localeName' => $localeName,
            'locales' => $locales,
            'localeNames' => $localeNames,
            'languageSwitcher' => $languageSwitcher,
        ]);
    }
    
    /**
     * 切换语言
     *
     * @return \think\Response
     */
    public function switchLanguage()
    {
        $locale = input('locale');
        
        if (!$locale) {
            return json(['success' => false, 'message' => '语言不能为空']);
        }
        
        $success = LanguageSwitcher::switchLocale($locale);
        
        if ($success) {
            // 刷新当前窗口
            Window::reload();
            
            return json(['success' => true, 'message' => '语言切换成功']);
        } else {
            return json(['success' => false, 'message' => '语言切换失败']);
        }
    }
    
    /**
     * 获取翻译
     *
     * @return \think\Response
     */
    public function translate()
    {
        $key = input('key');
        $replace = input('replace', []);
        $locale = input('locale');
        
        if (!$key) {
            return json(['success' => false, 'message' => '翻译键不能为空']);
        }
        
        $translation = Translator::get($key, $replace, $locale);
        
        return json(['success' => true, 'translation' => $translation]);
    }
    
    /**
     * 格式化日期
     *
     * @return \think\Response
     */
    public function formatDate()
    {
        $date = input('date', date('Y-m-d H:i:s'));
        $format = input('format', 'Y-m-d H:i:s');
        $locale = input('locale', Translator::getLocale());
        
        // 设置本地化环境
        $oldLocale = setlocale(LC_TIME, 0);
        setlocale(LC_TIME, $locale . '.UTF-8');
        
        // 格式化日期
        $timestamp = strtotime($date);
        $formatted = strftime($format, $timestamp);
        
        // 恢复本地化环境
        setlocale(LC_TIME, $oldLocale);
        
        return json(['success' => true, 'formatted' => $formatted]);
    }
    
    /**
     * 格式化数字
     *
     * @return \think\Response
     */
    public function formatNumber()
    {
        $number = input('number', 0);
        $decimals = input('decimals', 2);
        $decPoint = input('dec_point', '.');
        $thousandsSep = input('thousands_sep', ',');
        
        $formatted = number_format($number, $decimals, $decPoint, $thousandsSep);
        
        return json(['success' => true, 'formatted' => $formatted]);
    }
    
    /**
     * 格式化货币
     *
     * @return \think\Response
     */
    public function formatCurrency()
    {
        $amount = input('amount', 0);
        $locale = input('locale', Translator::getLocale());
        
        // 根据语言设置货币符号
        $currencySymbols = [
            'zh-cn' => '¥',
            'en-us' => '$',
            'ja-jp' => '¥',
            'ko-kr' => '₩',
            'fr-fr' => '€',
            'de-de' => '€',
            'es-es' => '€',
            'it-it' => '€',
            'pt-br' => 'R$',
            'ru-ru' => '₽',
        ];
        
        $symbol = $currencySymbols[$locale] ?? '$';
        
        // 格式化货币
        $formatted = $symbol . number_format($amount, 2, '.', ',');
        
        return json(['success' => true, 'formatted' => $formatted]);
    }
}
```

### 视图

#### 主页面 (index.html)

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{:Translator::get('app.name')} - {:Translator::get('settings.language')}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container mt-5">
        <h1>{:Translator::get('app.name')}</h1>
        <p class="lead">{:Translator::get('app.description')}</p>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>{:Translator::get('settings.language')}</h2>
            </div>
            <div class="card-body">
                <p>{:Translator::get('common.current')}: <strong>{$localeName}</strong> ({$locale})</p>
                
                <h3>{:Translator::get('common.available')}:</h3>
                <ul>
                    {foreach $localeNames as $code => $name}
                    <li>{$name} ({$code})</li>
                    {/foreach}
                </ul>
                
                <h3>{:Translator::get('common.switch')}:</h3>
                
                <div class="mb-4">
                    <h4>{:Translator::get('common.default')}:</h4>
                    {$languageSwitcher|raw}
                </div>
                
                <div class="mb-4">
                    <h4>Bootstrap:</h4>
                    {$languageSwitcherBootstrap|raw}
                </div>
                
                <div class="mb-4">
                    <h4>{:Translator::get('common.buttons')}:</h4>
                    {$languageSwitcherButtons|raw}
                </div>
                
                <div class="mb-4">
                    <h4>{:Translator::get('common.dropdown')}:</h4>
                    {$languageSwitcherDropdown|raw}
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>{:Translator::get('common.translation')}</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="translation-key" class="form-label">{:Translator::get('common.key')}:</label>
                    <input type="text" id="translation-key" class="form-control" value="common.hello">
                </div>
                
                <div class="mb-3">
                    <button id="translate-btn" class="btn btn-primary">{:Translator::get('common.translate')}</button>
                </div>
                
                <div class="mb-3">
                    <label for="translation-result" class="form-label">{:Translator::get('common.result')}:</label>
                    <input type="text" id="translation-result" class="form-control" readonly>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>{:Translator::get('common.formatting')}</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h3>{:Translator::get('common.date')}:</h3>
                        <p>{:date('Y-m-d H:i:s')}</p>
                    </div>
                    <div class="col-md-4">
                        <h3>{:Translator::get('common.number')}:</h3>
                        <p>1234567.89</p>
                    </div>
                    <div class="col-md-4">
                        <h3>{:Translator::get('common.currency')}:</h3>
                        <p>1234567.89</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{:url('i18n/settings')}" class="btn btn-outline-primary">{:Translator::get('settings.title')}</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/app.js"></script>
</body>
</html>
```

#### 设置页面 (settings.html)

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{:Translator::get('app.name')} - {:Translator::get('settings.title')}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container mt-5">
        <h1>{:Translator::get('settings.title')}</h1>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>{:Translator::get('settings.language')}</h2>
            </div>
            <div class="card-body">
                <p>{:Translator::get('common.current')}: <strong>{$localeName}</strong> ({$locale})</p>
                
                {$languageSwitcher|raw}
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>{:Translator::get('settings.appearance')}</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="theme" class="form-label">{:Translator::get('settings.theme')}:</label>
                    <select id="theme" class="form-select">
                        <option value="light">{:Translator::get('app.light')}</option>
                        <option value="dark">{:Translator::get('app.dark')}</option>
                        <option value="system">{:Translator::get('app.system')}</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h2>{:Translator::get('settings.advanced')}</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <button id="reset-settings" class="btn btn-danger">{:Translator::get('settings.reset')}</button>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{:url('i18n/index')}" class="btn btn-outline-primary">{:Translator::get('common.back')}</a>
            <button id="save-settings" class="btn btn-primary">{:Translator::get('settings.save_changes')}</button>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/app.js"></script>
</body>
</html>
```

### JavaScript 代码

```javascript
document.addEventListener('DOMContentLoaded', function() {
    // 翻译按钮
    const translateBtn = document.getElementById('translate-btn');
    if (translateBtn) {
        translateBtn.addEventListener('click', function() {
            const key = document.getElementById('translation-key').value;
            
            fetch('/i18n/translate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ key }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('translation-result').value = data.translation;
                    } else {
                        alert(data.message);
                    }
                });
        });
    }
    
    // 保存设置按钮
    const saveSettingsBtn = document.getElementById('save-settings');
    if (saveSettingsBtn) {
        saveSettingsBtn.addEventListener('click', function() {
            const theme = document.getElementById('theme').value;
            
            fetch('/settings/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ theme }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                });
        });
    }
    
    // 重置设置按钮
    const resetSettingsBtn = document.getElementById('reset-settings');
    if (resetSettingsBtn) {
        resetSettingsBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to reset all settings?')) {
                fetch('/settings/reset', {
                    method: 'POST',
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message);
                        }
                    });
            }
        });
    }
});
```

### CSS 样式

```css
body {
    padding-bottom: 50px;
}

.language-switcher {
    margin-bottom: 20px;
}

.card {
    margin-bottom: 30px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #f8f9fa;
}

.card-header h2 {
    margin: 0;
    font-size: 1.5rem;
}

.card-header h3 {
    margin: 0;
    font-size: 1.2rem;
}

.btn-group {
    margin-bottom: 15px;
}
```

## 总结

本示例展示了如何使用 NativePHP for ThinkPHP 的国际化功能，包括：

1. 使用 Translator 类进行文本翻译
2. 使用 LanguageSwitcher 组件切换语言
3. 根据语言格式化日期、时间、数字和货币
4. 保存用户的语言偏好设置

这些功能可以帮助开发者创建多语言的桌面应用程序，提高应用程序的国际化水平和用户体验。
