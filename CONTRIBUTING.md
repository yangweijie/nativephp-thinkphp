# 贡献指南

感谢您考虑为 NativePHP-ThinkPHP 项目做出贡献！以下是一些指导原则，帮助您为项目做出贡献。

## 行为准则

本项目采用 [贡献者公约](https://www.contributor-covenant.org/zh-cn/version/2/0/code_of_conduct/) 行为准则。通过参与本项目，您同意遵守其条款。

## 如何贡献

### 报告 Bug

如果您发现了 Bug，请创建一个 Issue，并包含以下信息：

- Bug 的详细描述
- 重现步骤
- 预期行为
- 实际行为
- 截图（如果适用）
- 环境信息（操作系统、PHP 版本、ThinkPHP 版本等）

### 提出新功能

如果您想提出新功能，请创建一个 Issue，并包含以下信息：

- 功能的详细描述
- 为什么这个功能对项目有价值
- 如何实现这个功能的建议（如果有）

### 提交代码

1. Fork 本仓库
2. 创建您的特性分支 (`git checkout -b feature/amazing-feature`)
3. 提交您的更改 (`git commit -m 'Add some amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 创建一个 Pull Request

### 代码风格

本项目遵循 PSR-12 代码风格。请确保您的代码符合这一标准。

## 开发环境设置

### 安装依赖

```bash
composer install
```

### 运行测试

```bash
composer test
```

### 运行代码风格检查

```bash
composer check-style
```

### 运行代码风格修复

```bash
composer fix-style
```

## 发布流程

1. 更新版本号
2. 更新 CHANGELOG.md
3. 创建一个新的 Release

## 许可证

通过贡献您的代码，您同意您的贡献将在 MIT 许可证下发布。
