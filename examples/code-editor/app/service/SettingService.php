<?php

namespace app\service;

use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;

class SettingService
{
    /**
     * 获取设置
     *
     * @return array
     */
    public function getSettings()
    {
        $defaultSettings = [
            'editor' => [
                'theme' => 'vs-dark',
                'fontSize' => 14,
                'fontFamily' => 'Consolas, "Courier New", monospace',
                'tabSize' => 4,
                'insertSpaces' => true,
                'wordWrap' => 'off',
                'autoSave' => false,
                'formatOnSave' => false,
                'minimap' => true,
                'lineNumbers' => true,
                'renderWhitespace' => 'none',
                'rulers' => [],
                'cursorStyle' => 'line',
                'cursorBlinking' => 'blink',
                'scrollBeyondLastLine' => true,
                'smoothScrolling' => false,
                'mouseWheelZoom' => true,
                'suggestOnTriggerCharacters' => true,
                'acceptSuggestionOnEnter' => 'on',
                'snippetSuggestions' => 'inline',
                'formatOnType' => false,
                'formatOnPaste' => false,
                'autoIndent' => 'full',
                'autoClosingBrackets' => 'languageDefined',
                'autoClosingQuotes' => 'languageDefined',
                'autoSurround' => 'languageDefined',
                'codeLens' => true,
                'folding' => true,
                'foldingStrategy' => 'auto',
                'showFoldingControls' => 'mouseover',
                'matchBrackets' => 'always',
                'occurrencesHighlight' => true,
                'renderControlCharacters' => false,
                'renderIndentGuides' => true,
                'renderLineHighlight' => 'all',
                'useTabStops' => true,
                'fontLigatures' => false,
                'links' => true,
                'colorDecorators' => true,
                'comments' => [
                    'insertSpace' => true,
                    'ignoreEmptyLines' => false,
                ],
                'accessibilitySupport' => 'auto',
                'screenReaderAnnounceInlineSuggestion' => true,
                'quickSuggestions' => true,
                'quickSuggestionsDelay' => 10,
                'parameterHints' => true,
                'iconsInSuggestions' => true,
                'codeActionsOnSave' => [],
                'semanticHighlighting' => true,
                'bracketPairColorization' => true,
                'guides' => [
                    'bracketPairs' => true,
                    'indentation' => true,
                    'highlightActiveIndentation' => true,
                ],
            ],
            'terminal' => [
                'fontSize' => 14,
                'fontFamily' => 'Consolas, "Courier New", monospace',
                'cursorStyle' => 'block',
                'cursorBlink' => true,
                'scrollback' => 1000,
                'copyOnSelect' => false,
                'rightClickBehavior' => 'default',
                'shell' => '',
                'shellArgs' => [],
                'env' => [],
                'allowChords' => true,
                'allowMouse' => true,
                'macOptionIsMeta' => false,
                'macOptionClickForcesSelection' => false,
                'altClickMovesCursor' => true,
                'fastScrollSensitivity' => 5,
                'scrollSensitivity' => 1,
                'rendererType' => 'canvas',
                'wordSeparator' => ' ()[]{}\',"`',
                'enableBell' => false,
                'bellSound' => '',
                'confirmOnExit' => false,
                'enableFileLinks' => true,
                'showExitAlert' => true,
                'theme' => 'dark',
            ],
            'ui' => [
                'theme' => 'dark',
                'sidebarPosition' => 'left',
                'sidebarWidth' => 250,
                'showStatusBar' => true,
                'showActivityBar' => true,
                'showMenuBar' => true,
                'showTabs' => true,
                'showMinimap' => true,
                'showBreadcrumbs' => true,
                'showIndentGuides' => true,
                'showLineNumbers' => true,
                'showGutter' => true,
                'showScrollbars' => true,
                'showRulers' => false,
                'showFoldingControls' => true,
                'showWhitespace' => false,
                'showInvisibles' => false,
                'showLineEndings' => false,
                'showIndentations' => true,
                'showEmptySpace' => false,
                'showLineHighlight' => true,
                'showCursorPosition' => true,
                'showSelectionHighlight' => true,
                'showMatchingBrackets' => true,
                'showOverviewRuler' => true,
                'showCodeLens' => true,
                'showHints' => true,
                'showErrors' => true,
                'showWarnings' => true,
                'showInfos' => true,
                'showAnnotations' => true,
                'showDecorations' => true,
                'showGlyphs' => true,
                'showIcons' => true,
                'showSymbols' => true,
                'showOutline' => true,
                'showProblems' => true,
                'showOutput' => true,
                'showDebug' => true,
                'showTerminal' => true,
                'showSearch' => true,
                'showReplace' => true,
                'showFind' => true,
                'showGoTo' => true,
                'showCommands' => true,
                'showHelp' => true,
                'showAbout' => true,
                'showSettings' => true,
                'showExtensions' => true,
                'showUpdates' => true,
                'showNotifications' => true,
                'showWelcome' => true,
                'showStartPage' => true,
                'showTips' => true,
                'showTours' => true,
                'showReleaseNotes' => true,
                'showChangelog' => true,
                'showLicense' => true,
                'showPrivacyPolicy' => true,
                'showTermsOfService' => true,
                'showCommunity' => true,
                'showBlog' => true,
                'showTwitter' => true,
                'showGitHub' => true,
                'showDiscord' => true,
                'showSlack' => true,
                'showForum' => true,
                'showIssues' => true,
                'showPullRequests' => true,
                'showWiki' => true,
                'showDocs' => true,
                'showFAQ' => true,
                'showSupport' => true,
                'showFeedback' => true,
                'showSurvey' => true,
                'showRating' => true,
                'showShare' => true,
                'showDonate' => true,
                'showSponsors' => true,
                'showPatrons' => true,
                'showBackers' => true,
                'showContributors' => true,
                'showAuthors' => true,
                'showMaintainers' => true,
                'showOwners' => true,
                'showTeam' => true,
                'showOrganization' => true,
                'showCompany' => true,
                'showBrand' => true,
                'showLogo' => true,
                'showName' => true,
                'showVersion' => true,
                'showBuildNumber' => true,
                'showBuildDate' => true,
                'showBuildTime' => true,
                'showBuildCommit' => true,
                'showBuildBranch' => true,
                'showBuildTag' => true,
                'showBuildAuthor' => true,
                'showBuildEmail' => true,
                'showBuildURL' => true,
                'showBuildID' => true,
                'showBuildType' => true,
                'showBuildPlatform' => true,
                'showBuildArch' => true,
                'showBuildOS' => true,
                'showBuildCPU' => true,
                'showBuildRAM' => true,
                'showBuildGPU' => true,
                'showBuildScreen' => true,
                'showBuildResolution' => true,
                'showBuildDPI' => true,
                'showBuildLanguage' => true,
                'showBuildLocale' => true,
                'showBuildTimezone' => true,
                'showBuildCountry' => true,
                'showBuildRegion' => true,
                'showBuildCity' => true,
                'showBuildISP' => true,
                'showBuildIP' => true,
                'showBuildMAC' => true,
                'showBuildHostname' => true,
                'showBuildUsername' => true,
                'showBuildHome' => true,
                'showBuildTemp' => true,
                'showBuildCache' => true,
                'showBuildConfig' => true,
                'showBuildData' => true,
                'showBuildLogs' => true,
                'showBuildBackups' => true,
                'showBuildUpdates' => true,
                'showBuildPlugins' => true,
                'showBuildExtensions' => true,
                'showBuildThemes' => true,
                'showBuildSnippets' => true,
                'showBuildKeybindings' => true,
                'showBuildSettings' => true,
                'showBuildPreferences' => true,
                'showBuildState' => true,
                'showBuildHistory' => true,
                'showBuildBookmarks' => true,
                'showBuildFavorites' => true,
                'showBuildRecent' => true,
                'showBuildOpen' => true,
                'showBuildClosed' => true,
                'showBuildActive' => true,
                'showBuildInactive' => true,
                'showBuildEnabled' => true,
                'showBuildDisabled' => true,
                'showBuildInstalled' => true,
                'showBuildUninstalled' => true,
                'showBuildAvailable' => true,
                'showBuildUnavailable' => true,
                'showBuildCompatible' => true,
                'showBuildIncompatible' => true,
                'showBuildDeprecated' => true,
                'showBuildObsolete' => true,
                'showBuildNew' => true,
                'showBuildOld' => true,
                'showBuildStable' => true,
                'showBuildUnstable' => true,
                'showBuildBeta' => true,
                'showBuildAlpha' => true,
                'showBuildRC' => true,
                'showBuildDev' => true,
                'showBuildNightly' => true,
                'showBuildDaily' => true,
                'showBuildWeekly' => true,
                'showBuildMonthly' => true,
                'showBuildQuarterly' => true,
                'showBuildYearly' => true,
                'showBuildLTS' => true,
                'showBuildESR' => true,
                'showBuildEOL' => true,
                'showBuildEOS' => true,
            ],
            'git' => [
                'enabled' => true,
                'autoFetch' => true,
                'autoFetchInterval' => 300,
                'autoStash' => true,
                'autoStashOnPull' => true,
                'autoStashOnCheckout' => true,
                'autoPush' => false,
                'autoPushOnCommit' => false,
                'autoCommit' => false,
                'autoCommitOnSave' => false,
                'autoCommitMessage' => 'Auto commit: {files}',
                'autoCommitInterval' => 300,
                'autoCommitMinChanges' => 1,
                'autoCommitMaxChanges' => 100,
                'autoCommitMaxFiles' => 100,
                'autoCommitMaxSize' => 1048576,
                'autoCommitMaxTime' => 300,
                'autoCommitMaxIdle' => 300,
                'autoCommitMaxActive' => 300,
                'autoCommitMaxTotal' => 300,
                'autoCommitMaxHistory' => 100,
                'autoCommitMaxUndo' => 100,
                'autoCommitMaxRedo' => 100,
                'autoCommitMaxStack' => 100,
                'autoCommitMaxQueue' => 100,
                'autoCommitMaxBuffer' => 100,
                'autoCommitMaxCache' => 100,
                'autoCommitMaxMemory' => 100,
                'autoCommitMaxCPU' => 100,
                'autoCommitMaxDisk' => 100,
                'autoCommitMaxNet' => 100,
                'autoCommitMaxIO' => 100,
                'autoCommitMaxLoad' => 100,
                'autoCommitMaxTemp' => 100,
                'autoCommitMaxBattery' => 100,
                'autoCommitMaxPower' => 100,
                'autoCommitMaxEnergy' => 100,
                'autoCommitMaxTime' => 100,
                'autoCommitMaxDate' => 100,
                'autoCommitMaxDay' => 100,
                'autoCommitMaxWeek' => 100,
                'autoCommitMaxMonth' => 100,
                'autoCommitMaxYear' => 100,
                'autoCommitMaxDecade' => 100,
                'autoCommitMaxCentury' => 100,
                'autoCommitMaxMillennium' => 100,
                'autoCommitMaxEra' => 100,
                'autoCommitMaxEpoch' => 100,
                'autoCommitMaxEon' => 100,
                'autoCommitMaxEternity' => 100,
            ],
            'debug' => [
                'enabled' => true,
                'breakpoints' => true,
                'watchExpressions' => true,
                'callStack' => true,
                'variables' => true,
                'console' => true,
                'output' => true,
                'problems' => true,
                'breakpointsView' => true,
                'watchView' => true,
                'callStackView' => true,
                'variablesView' => true,
                'consoleView' => true,
                'outputView' => true,
                'problemsView' => true,
                'debugToolbar' => true,
                'debugStatusBar' => true,
                'debugConsole' => true,
                'debugRepl' => true,
                'debugActions' => true,
                'debugButtons' => true,
                'debugIcons' => true,
                'debugColors' => true,
                'debugTheme' => true,
                'debugFont' => true,
                'debugFontSize' => true,
                'debugFontFamily' => true,
                'debugFontWeight' => true,
                'debugFontStyle' => true,
                'debugFontVariant' => true,
                'debugFontStretch' => true,
                'debugFontKerning' => true,
                'debugFontFeatures' => true,
                'debugFontVariations' => true,
                'debugFontSynthesis' => true,
                'debugFontLanguage' => true,
                'debugFontLocale' => true,
                'debugFontSystem' => true,
                'debugFontFallback' => true,
                'debugFontAlternate' => true,
                'debugFontSubstitute' => true,
                'debugFontOverride' => true,
                'debugFontReplace' => true,
                'debugFontMap' => true,
                'debugFontMatch' => true,
                'debugFontSelect' => true,
                'debugFontLoad' => true,
                'debugFontRender' => true,
                'debugFontDisplay' => true,
                'debugFontVisibility' => true,
                'debugFontOpacity' => true,
                'debugFontTransform' => true,
                'debugFontAnimation' => true,
                'debugFontTransition' => true,
                'debugFontEffect' => true,
                'debugFontFilter' => true,
                'debugFontMask' => true,
                'debugFontClip' => true,
                'debugFontComposite' => true,
                'debugFontBlend' => true,
                'debugFontMix' => true,
                'debugFontCombine' => true,
                'debugFontMerge' => true,
                'debugFontJoin' => true,
                'debugFontSplit' => true,
                'debugFontSeparate' => true,
                'debugFontDivide' => true,
                'debugFontIsolate' => true,
                'debugFontContain' => true,
                'debugFontInclude' => true,
                'debugFontExclude' => true,
                'debugFontIgnore' => true,
                'debugFontSkip' => true,
                'debugFontPass' => true,
                'debugFontFail' => true,
                'debugFontError' => true,
                'debugFontWarning' => true,
                'debugFontInfo' => true,
                'debugFontDebug' => true,
                'debugFontLog' => true,
                'debugFontTrace' => true,
                'debugFontVerbose' => true,
                'debugFontSilent' => true,
                'debugFontQuiet' => true,
                'debugFontLoud' => true,
                'debugFontNoisy' => true,
                'debugFontCrazy' => true,
                'debugFontInsane' => true,
                'debugFontMad' => true,
                'debugFontWild' => true,
                'debugFontExtreme' => true,
                'debugFontUltimate' => true,
                'debugFontMaximum' => true,
                'debugFontMinimum' => true,
                'debugFontOptimum' => true,
                'debugFontNormal' => true,
                'debugFontDefault' => true,
                'debugFontStandard' => true,
                'debugFontRegular' => true,
                'debugFontPlain' => true,
                'debugFontSimple' => true,
                'debugFontBasic' => true,
                'debugFontCommon' => true,
                'debugFontGeneric' => true,
                'debugFontUniversal' => true,
                'debugFontGlobal' => true,
                'debugFontLocal' => true,
                'debugFontCustom' => true,
                'debugFontUser' => true,
                'debugFontSystem' => true,
                'debugFontBrowser' => true,
                'debugFontPlatform' => true,
                'debugFontDevice' => true,
                'debugFontOS' => true,
                'debugFontEnvironment' => true,
                'debugFontContext' => true,
                'debugFontSituation' => true,
                'debugFontCondition' => true,
                'debugFontState' => true,
                'debugFontMode' => true,
                'debugFontProfile' => true,
                'debugFontPreset' => true,
                'debugFontTemplate' => true,
                'debugFontTheme' => true,
                'debugFontStyle' => true,
                'debugFontDesign' => true,
                'debugFontLayout' => true,
                'debugFontRender' => true,
                'debugFontDisplay' => true,
                'debugFontOutput' => true,
                'debugFontResult' => true,
                'debugFontFinal' => true,
                'debugFontEnd' => true,
            ],
        ];
        
        $settings = Settings::get('editor.settings', []);
        
        return array_merge_recursive($defaultSettings, $settings);
    }
    
    /**
     * 更新设置
     *
     * @param array $settings
     * @return bool
     */
    public function updateSettings($settings)
    {
        Settings::set('editor.settings', $settings);
        
        // 应用设置到所有窗口
        $windows = Window::all();
        foreach ($windows as $window) {
            $window->webContents->executeJavaScript('applySettings(' . json_encode($settings) . ')');
        }
        
        // 发送通知
        Notification::send('设置已更新', '您的设置已成功更新');
        
        return true;
    }
    
    /**
     * 获取主题列表
     *
     * @return array
     */
    public function getThemes()
    {
        $themesDir = public_path() . '/static/themes';
        
        if (!is_dir($themesDir)) {
            FileSystem::makeDirectory($themesDir, 0755, true);
        }
        
        $themes = [
            [
                'id' => 'vs',
                'name' => 'Visual Studio',
                'type' => 'light',
                'builtin' => true,
            ],
            [
                'id' => 'vs-dark',
                'name' => 'Visual Studio Dark',
                'type' => 'dark',
                'builtin' => true,
            ],
            [
                'id' => 'hc-black',
                'name' => 'High Contrast Black',
                'type' => 'dark',
                'builtin' => true,
            ],
            [
                'id' => 'hc-light',
                'name' => 'High Contrast Light',
                'type' => 'light',
                'builtin' => true,
            ],
        ];
        
        $files = FileSystem::files($themesDir);
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') {
                continue;
            }
            
            $theme = json_decode(FileSystem::read($file), true);
            
            if (!$theme || !isset($theme['name'])) {
                continue;
            }
            
            $themes[] = [
                'id' => pathinfo($file, PATHINFO_FILENAME),
                'name' => $theme['name'],
                'type' => $theme['type'] ?? 'dark',
                'builtin' => false,
                'path' => $file,
            ];
        }
        
        return $themes;
    }
    
    /**
     * 设置主题
     *
     * @param string $themeId
     * @return bool
     */
    public function setTheme($themeId)
    {
        $settings = $this->getSettings();
        $settings['editor']['theme'] = $themeId;
        
        $this->updateSettings($settings);
        
        return true;
    }
    
    /**
     * 导出设置
     *
     * @return string|false
     */
    public function exportSettings()
    {
        $settings = $this->getSettings();
        $json = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        $path = Dialog::saveFile([
            'title' => '导出设置',
            'defaultPath' => 'settings.json',
            'filters' => [
                ['name' => 'JSON', 'extensions' => ['json']],
            ],
        ]);
        
        if (!$path) {
            return false;
        }
        
        FileSystem::write($path, $json);
        
        // 发送通知
        Notification::send('设置已导出', '您的设置已成功导出到 ' . basename($path));
        
        return $path;
    }
    
    /**
     * 导入设置
     *
     * @return bool
     */
    public function importSettings()
    {
        $path = Dialog::openFile([
            'title' => '导入设置',
            'filters' => [
                ['name' => 'JSON', 'extensions' => ['json']],
            ],
        ]);
        
        if (!$path) {
            return false;
        }
        
        $json = FileSystem::read($path);
        $settings = json_decode($json, true);
        
        if (!$settings) {
            // 发送通知
            Notification::send('导入失败', '无法解析设置文件');
            
            return false;
        }
        
        $this->updateSettings($settings);
        
        // 发送通知
        Notification::send('设置已导入', '您的设置已成功导入');
        
        return true;
    }
    
    /**
     * 重置设置
     *
     * @return bool
     */
    public function resetSettings()
    {
        Settings::forget('editor.settings');
        
        // 应用默认设置到所有窗口
        $defaultSettings = $this->getSettings();
        $windows = Window::all();
        foreach ($windows as $window) {
            $window->webContents->executeJavaScript('applySettings(' . json_encode($defaultSettings) . ')');
        }
        
        // 发送通知
        Notification::send('设置已重置', '您的设置已恢复为默认值');
        
        return true;
    }
}
