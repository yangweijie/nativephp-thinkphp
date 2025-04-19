# 贡献指南

感谢你考虑为 NativePHP for ThinkPHP 做出贡献！本文档将指导你如何为项目做出贡献。

## 行为准则

参与本项目的所有贡献者都应遵守我们的行为准则。请确保你的行为符合社区期望。

## 如何贡献

### 报告 Bug

如果你发现了 Bug，请通过 GitHub Issues 报告。在报告 Bug 时，请提供以下信息：

1. 使用的 NativePHP for ThinkPHP 版本
2. 使用的 PHP 和 ThinkPHP 版本
3. 使用的操作系统和版本
4. 重现 Bug 的步骤
5. 预期行为和实际行为
6. 相关日志和错误信息
7. 如果可能，提供一个最小化的示例代码

### 提出新功能

如果你有新功能的想法，请通过 GitHub Issues 提出。在提出新功能时，请提供以下信息：

1. 功能描述
2. 使用场景
3. 如果可能，提供一个实现思路或示例代码

### 提交代码

如果你想为项目贡献代码，请按照以下步骤操作：

1. Fork 项目仓库
2. 创建一个新的分支（`git checkout -b feature/your-feature` 或 `git checkout -b fix/your-fix`）
3. 编写代码
4. 编写测试
5. 运行测试（`composer test`）
6. 提交代码（`git commit -m "Add feature/fix: your description"`）
7. 推送到你的 Fork（`git push origin feature/your-feature`）
8. 创建一个 Pull Request

### 代码风格

本项目遵循 PSR-12 代码风格。请确保你的代码符合这一标准。你可以使用 PHP_CodeSniffer 检查代码风格：

```bash
composer cs-check
```

如果有不符合规范的地方，你可以使用以下命令自动修复：

```bash
composer cs-fix
```

### 测试

请为你的代码编写测试。本项目使用 PHPUnit 进行测试。你可以使用以下命令运行测试：

```bash
composer test
```

## 开发环境

### 安装依赖

```bash
composer install
```

### 运行测试

```bash
composer test
```

### 检查代码风格

```bash
composer cs-check
```

### 自动修复代码风格

```bash
composer cs-fix
```

### 生成文档

```bash
composer docs
```

## 分支策略

- `master` 分支是稳定分支，包含最新的发布版本
- `develop` 分支是开发分支，包含最新的开发版本
- 功能分支应该从 `develop` 分支创建，命名为 `feature/your-feature`
- 修复分支应该从 `master` 分支创建，命名为 `fix/your-fix`

## 版本控制

本项目遵循 [语义化版本控制](https://semver.org/lang/zh-CN/)。

## 发布流程

1. 更新 `CHANGELOG.md`
2. 更新版本号
3. 创建一个新的 Git 标签
4. 推送标签到 GitHub
5. 创建一个新的 GitHub Release

## 文档

请为你的代码编写文档。本项目使用 MkDocs 生成文档。你可以使用以下命令生成文档：

```bash
composer docs
```

## 许可证

通过为本项目做出贡献，你同意你的贡献将根据项目的 MIT 许可证进行许可。

## 联系方式

如果你有任何问题，可以通过以下方式联系我们：

- GitHub Issues
- 电子邮件：[your-email@example.com](mailto:your-email@example.com)

## 致谢

感谢所有为本项目做出贡献的人！
