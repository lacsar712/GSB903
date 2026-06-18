# Sunny Day Coffee Shop (晴天咖啡馆) ☕️

这是一个基于原生 PHP + HTML + JavaScript 架构开发的现代咖啡馆在线点单应用。项目专为 **Windows phpStudy (小皮面板)** 运行环境进行深度重构封装，彻底剥离了 Docker 与前端编译环境，支持真正的“开箱即用、一键双击”部署体验。

---

## 🎯 技术栈说明

完全原生化的轻量架构，让作业展示与测试畅通无阻：

- **基础运行环境**: Windows phpStudy (推荐 Nginx/Apache + PHP > 8.0 + MySQL)
- **极简后端 API**: Vanilla PHP + 轻量 MVC (Bramus Router 路由 + Eloquent ORM 数据库引擎)
- **纯净前端页面**: 完全抛弃 React 等编译耗时框架，采用**纯静态原生 HTML + Vanilla JS** 结合 Fetch API 渲染动态数据
- **轻量 UI 方案**: TailwindCSS (CDN 引入) 原生打造的高颜值 UI + Lucide 图标库

---

## 🚀 小皮面板测试与运行指南（如果本地没有xiao pi下方提供了docker验证的方式）

### 第一步：代码就位与依赖安装
1. 将获取到的本 `label-903` 项目文件夹，直接拷贝放置到小皮面板的网站根目录。
2. 启动小皮面板，并确保开启了 **Nginx / Apache** 和 **MySQL 服务**。
3. **关键步骤**：由于本项目依赖了上游扩展包（Bramus 路由、Eloquent ORM 等），请务必在项目根目录下打开终端，运行 `composer install` 以安装并生成 `vendor` 文件夹。

### 第二步：数据库就绪
1. 在小皮面板左侧选择 **“数据库”**，创建一个新的数据库，命名设置为：`sunny_coffee`。
2. 点击“管理”打开 phpMyAdmin，导入 `db/init.sql` 脚本完成初始化。



## 🌟 核心特性 (Features)

- **甄选菜单**: 响应式且精美的商品展示，支持分类过滤。
- **点单系统 (新)**: 完整的订单业务逻辑，支持用户下单与数据库持久化。
- **醇香留言**: 支持 XSS 拦截的安全留言板，实时分享品质体验。
- **管理中台**: 深度落地的 CRUD 商品管理，支持新增、编辑、删除。
- **安全架构**: 
  - JWT 鉴权：使用环境变量 `JWT_SECRET` 加密，拒绝硬编码。
  - XSS 防护：全局使用 `escapeHTML` 渲染用户生成内容。
  - 密码安全：采用 `password_hash` & `password_verify` 进行 BCrypt 加密。

## 🛋 亮点功能 (Featured Highlights)

1. **真实点单业务 (New)** ☕️
   - **闭环下单**：用户登录后，点击商品卡片的“立即点单”，后台会实时向 `orders` 表写入对应数据。
   - **历史追溯**：导航栏右侧新增“我的点单”图标，点击即可在精美的模态框中实时追踪自己的点单记录与状态（如“准备中”、“已送达”）。
   
2. **管理端深度赋能 (Updated)** 🛠
   - **全 CRUD 覆盖**：除了基本的新增与删除，现在支持对现有菜单进行**实时编辑与修改**。
   - **平滑交互**：修改过程采用异步 `PUT` 请求，所见即所得，无需页面刷新。

3. **视觉美学 2.0 (Wow Design)** ✨
   - **Premium UI**：全站基于毛玻璃（Glassmorphism）与现代卡片流（Modern Card Flow）打造，色彩调配优雅。
   - **智能反馈**：通过全新的 **Toast 通知系统**，所有成功、报错或授权异常都以非阻塞的方式进行友好提醒。


## 🏗 技术栈 (Tech Stack)

- **Frontend**: Vanilla HTML5, TailwindCSS, Lucide Icons, JavaScript (ES6+).
- **Backend**: PHP 8.2 (Apache), Composer, Bramus Router.
- **ORM**: Eloquent ORM (Illuminate Database).
- **DB**: MySQL 8.0.
- **Tools**: Docker & Docker Compose.

## 🏗 目录结构

```
sunny-coffee-vibe/
├── src/
│   ├── Controllers/     # 业务逻辑（Auth, Product, Order, Message）
│   ├── Models/          # 数据库模型（Eloquent）
│   ├── Routes/          # API 路由定义
│   ├── Utils/           # 核心工具类（JWT 加密、DB 驱动）
│   └── Config/          # 全局配置
├── db/                  # 数据库初始化脚本
├── vendor/              # Composer 依赖库
├── app.js               # 前端核心逻辑与拦截器
├── index.html           # 首页 (甄选菜单)
├── guestbook.html       # 留言板
├── admin.html           # 后台管理
├── docker-compose.yml   # 环境容器编排
└── README.md
```

---

## 🐳 Mac/Linux 本地 Docker 快速测试指南 (附赠方案)

本项目本质上是提取交付的纯净 PHP 原生代码。但由于只在部分特定开发机 (例如 Mac / Linux) 上由于没有自带 PHP 解析器引擎和 MySQL 等原因无法像 Windows 小皮面板一样原生直接运行拉起后端，并且双击 `html` 属于 `file:///` 本地协议会被浏览器强制阻止后端 Ajax 请求：

因此为了让你现在**在不切换操作系统的电脑上也能立刻开箱完美体验和测试代码**，本目录**特别额外赠送并保留**了一份极简的本地压测容器文件 `docker-compose.yml` (其内部搭载了兼容小皮环境的 Apache+PHP 引擎)：

1. 确保电脑已安装 Docker 环境；
2. 在该代码库目录下打开终端，执行一键拉起命令：
   ```bash
   docker-compose up -d
   ```
   *(执行后请等待大约 15 秒，由容器化特性内置逻辑会帮你自动初始化连接和写入测试表数据)*
3. 容器就绪后，打开浏览器访问自动被挂载好的服务端口：
   🌍 [http://localhost:903/index.html](http://localhost:903/index.html)
   
> ⚠️ **提示：** 经过此方案，在 Mac 下也能达到和放置在 Windows 小皮面板几乎一摸一样的原生代码解析效果。当测试完成后如果不需要可以随时删去该 YAML 配置文件。

---

## 🔑 初始账号

为方便直接测试后台和功能交互，已在 SQL 中注入初始账号结构：
- 🌟 **店长（管理员）**: `admin` / `root123` (拥有登入后台商品管理后台的最高权限)
- 👤 **默认顾客**: `testuser` / `123456` (普通查看菜单与留言板的权限)

---

## ✨ 核心模块演示流程

在测试体验时，推荐遵循以下流程体验应用闭环：
1. **纯直出式滤客点单板 (首页)**：加载界面，观察分类按钮切换（纯本地状态机筛选咖啡与甜品列表）；
2. **凭证分发 (登录板)**：进入登录页使用账号登录，观察 Token 持久化写入以及 Navbar 导航条权限组件的热更新变化；
3. **极简后台管理 (店长专属后台)**：管理员登入后头部自动显现“后台管理”按钮，进入可视化的控制面板体验商品的 CRUD （增、修、撤）业务操作，所见即所得；
4. **访客留言板 (用户端互动)**：以测试用户姿态登入后进入留言界面，体验纯 Fetch 原生驱动实现的高交互率动态留言系统。
