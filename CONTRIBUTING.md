# 贡献指南

感谢您对 NativePHP for ThinkPHP 的贡献！本文档将指导您如何为项目做出贡献。

## 行为准则

请确保您的行为符合我们的行为准则。我们希望所有贡献者都能尊重彼此，创造一个积极和包容的社区。

## 报告问题

如果您发现了问题或有功能请求，请在 GitHub 上创建一个 Issue。在创建 Issue 时，请提供以下信息：

- 问题的详细描述
- 复现步骤（如果适用）
- 预期行为和实际行为
- 截图（如果适用）
- 环境信息（操作系统、PHP 版本、ThinkPHP 版本等）

## 提交 Pull Request

1. Fork 项目
2. 创建您的特性分支 (`git checkout -b feature/amazing-feature`)
3. 提交您的更改 (`git commit -m 'Add some amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 创建一个 Pull Request

## 开发指南

### 环境设置

1. 克隆项目
2. 安装依赖：`composer install`
3. 设置开发环境：`composer install --dev`

### 代码风格

我们使用 PSR-12 代码风格。在提交代码之前，请确保您的代码符合 PSR-12 标准：

```bash
composer phpcs
```

您可以使用以下命令自动修复代码风格问题：

```bash
composer php-cs-fixer:fix
```

### 测试

在提交代码之前，请确保所有测试都能通过：

```bash
composer test
```

如果您添加了新功能，请为其编写测试。我们使用 PHPUnit 进行测试。

### 文档

如果您添加了新功能或修改了现有功能，请更新相应的文档。文档位于 `docs` 目录中。

## 发布流程

1. 更新版本号（遵循 [语义化版本](https://semver.org/lang/zh-CN/)）
2. 更新 CHANGELOG.md
3. 创建一个新的 GitHub Release
4. 发布到 Packagist

## 许可证

通过贡献代码，您同意您的贡献将根据项目的 MIT 许可证进行许可。
