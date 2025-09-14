# Car Maintenance App (PHP + MAMP)

##  概要
このアプリは **自分の車のメンテナンス記録を管理するためのWebアプリ** です。  
ログイン機能付きで、車両情報やメンテナンス履歴を簡単に記録・編集・削除できます。  

PHP・MySQL・MAMP を使った学習・ポートフォリオ用のプロジェクトです。

---

##  主な機能
- ユーザー認証（ログイン / ログアウト / 新規登録）
- 車両情報のCRUD
  - 追加（Create）
  - 一覧表示（Read）
  - 編集（Update）
  - 削除（Delete）
- CSRF対策付きフォーム
- セッションによるログイン管理

---

##  使用技術
- **言語**: PHP 8.x
- **データベース**: MySQL (MAMP環境)
- **ツール**: MAMP, ターミナル, VSCode, Git/GitHub
- **構成**: シンプルMVC風（public / app / views に分割）

---

##  セットアップ手順
1. リポジトリをクローン
   ```bash
   git clone https://github.com/RS-25cy/car-maint.git
   cd car-maint
