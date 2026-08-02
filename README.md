<img width="5464" height="3640" alt="S3　+ CloudFront(OAC)によるサーバーレス技術ブログ" src="https://github.com/user-attachments/assets/723069a3-2c80-4156-88c4-b659de4adfcd" />

## S3　+ CloudFront(OAC)によるサーバーレス技術ブログ

---

## 概要

このポートフォリオは、WordPressを**記事執筆・デザイン管理用のCMS**として使いながら、実際に公開するのはWordPressそのものではなく、そこから書き出した**静的サイト**にする、というインフラ構成です。

WordPress単体を直接公開すると、常時起動するEC2 + DBのコストや、プラグインの脆弱性など気にする点が増えます。そこで「更新作業(執筆・デザイン)はWordPressの使いやすい管理画面で行い、公開はS3 + CloudFrontの静的サイトで行う」という、いわゆる**Jamstack風の構成**にすることで、サーバー代を抑えつつ高速・安全な配信を実現しています。

現在、下記のURLで公開しています。
https://www.zerocla.com

1. **WordPress on EC2**: `terraform apply`だけでVPC・EC2・IAMロールが構築され、続けてAnsibleがApache・PHP・MySQL・WordPress本体・Cocoonテーマ(親テーマ+自作子テーマ)まで自動でセットアップします。GitHub Actionsにpushするだけで、まっさらな状態からブログが動く状態まで完全自動で到達することを目指しています。あくまでWordPress執筆作業の管理画面として使い、普段は停止するか削除します。
2. **S3 + CloudFront 静的サイト**: WordPressで記事を書き、`Simply Static`プラグインで静的化した上で、CloudFront(OAC)経由でHTTPS配信し、TLSはACM証明書を発行して行います。オリジンのS3バケットは非公開のままです。

## この構築の利点

### 非常に安価

通常のレンタルサーバーでWordPressサイトを公開する場合、プランにもよりますが、月額500〜1,000円程度の費用がかかることは珍しくありません。さらに、有料ドメインやSSL証明書、CDNなどのオプションを利用する場合は、追加費用が発生することもあります。

このブログでは、公開環境としてAmazon S3とCloudFrontを利用しています。料金は**保存容量やデータ転送量、リクエスト数に応じた従量課金制**で、個人ブログ程度のアクセスであれば、月数十〜数百円程度で運用できるケースもあります。また、SSL証明書にはACMを利用しているため、追加費用はかかりません。

### 高いセキュリティ

一般的なWordPressサイトでは、プラグインやテーマの脆弱性を狙った攻撃、PHPの実行を狙った不正アクセス、データベースへの攻撃（SQLインジェクションなど）といったリスクへの対策が常に必要になります。

一方、このブログの公開環境では、**PHPやデータベースが動作していません。**
配信されるのはHTML・CSS・JavaScript・画像などの静的ファイルのみのため、これらの攻撃対象の多くが存在しません。
さらにこの構成ではS3はCloudFrontを通して配信されるため、従来のS3パブリックアクセスでは防げないDDoS攻撃も防ぐことができます。

### 高可用かつスケーラブル

公開サイトはAmazon S3とCloudFrontから配信しています。
S3は高い耐久性と複数のAZからなる可用性があり、アクセスが集中してもスケールしやすく、CloudFrontにより世界中のエッジロケーションから高速配信が可能です。そのため管理コストも最小限に抑え、AWSの高い耐久性・可用性を活用することができます。

## 制作フロー(デザイン〜公開まで)

サイトのデザイン制作からインフラ構築まで、モダンなAIツールを一貫して活用しています。

1. **GPT Image 2.0** でブログトップページのデザイン・素材(ロゴ、アイコン等)を生成
2. **Claude(Design)** で、生成したデザイン・素材をもとにWordPress用のモックアップを作成
3. **Claude Code** に渡し、レスポンシブ対応を行いながらCocoonの子テーマ(`cocoon-child`)として実装
4. WordPress側で **Cocoon親テーマ + 自作子テーマ** を読み込み、CMSとして記事・固定ページを作成
5. **Simply Static** プラグインで静的サイト化
6. 生成したファイル一式を **S3 + CloudFront** に配信し、実際の公開はこちらで行う

デザイン制作からコーディング、インフラ構築・運用トラブルシューティングまで、AIツールを役割ごとに使い分けながら一気通貫で進めたプロジェクトです。

## 技術選定

**インフラプロビジョニング(IaC)**

- Terraformを採用
    - インフラ構成をコード化し、再現性と変更管理を担保するため
    - 既存インフラとのドリフト(手動変更との差分)を可視化・解消しながら運用する経験を積むため

**デプロイ(CI/CD)**

- GitHub Actionsを採用
    - pull requestで`terraform plan`、mainへのマージで`terraform apply`+ Ansible実行、という流れをGitHubだけで完結できるため
    - OIDCによりAWSアクセスキーをGitHub Secretsに置かずに済むため

**構成管理**

- Ansibleを採用
    - Apache/PHP/MySQL/WordPressのセットアップをコード化し、再現性を担保するため
    - SSM Session Manager経由で接続することで、EC2に22番ポートを一切開けずに済むため

**認証**

- GitHub Actions ⇔ AWS: OIDC(`sts:AssumeRoleWithWebIdentity`)
- GitHub Actions ⇔ EC2: SSM Session Manager(SSHキーペア不要)

## インフラ構成図
<img width="1295" height="674" alt="名称未設定のデザイン (2)" src="https://github.com/user-attachments/assets/b09459b2-9be1-4e8a-8a44-60c5e44718c0" />


## 全体構成

### WordPress on EC2

| 項目            | 設定値                                                                 |
| :-------------- | :--------------------------------------------------------------------- |
| リージョン      | 東京リージョン(ap-northeast-1)                                         |
| VPC             | `10.0.0.0/16`                                                          |
| Public Subnet   | `10.0.1.0/24`(ap-northeast-1a)                                         |
| EC2インスタンス | `t3.small`、Amazon Linux 2023                                          |
| アクセス方法    | SSM Session Manager(SSHキーペア不使用)                                 |
| IAMロール       | `demo-ssm-role`(`AmazonSSMManagedInstanceCore` + `AmazonS3FullAccess`) |
| Security Group  | Inbound: `80`/`443`のみ許可、`22`は開放していない                      |
| DB              | EC2上に同居させたMySQL 8.0(community repoからインストール)             |
| Webサーバー     | Apache + PHP-FPM                                                       |
| テーマ          | Cocoon親テーマ + 自作子テーマ(`cocoon-child`)                          |
| tfstate         | S3バックエンド(`aws-study-marube23-backet`)                            |

### S3 + CloudFront 静的サイト

| 項目            | 設定値                                                                                  |
| :-------------- | :-------------------------------------------------------------------------------------- |
| S3バケット      | `zerocla-marube23-bucket`(パブリックアクセスは全面ブロック)                             |
| アクセス制御    | CloudFront Origin Access Control(OAC)経由のみ許可するバケットポリシー                   |
| CDN             | CloudFront、独自ドメイン `www.zerocla.com`                                              |
| TLS             | ACM証明書(us-east-1)、`TLSv1.3_2025`を最低プロトコルバージョンに設定                    |
| URLルーティング | CloudFront Functionで末尾スラッシュ・拡張子なしURLに`index.html`を自動補完              |
| 配信元データ    | WordPressの`Simply Static`プラグインで生成した静的HTML一式を`aws s3 sync`でアップロード |

## GitHub Actions 概要

`WordPress/**/*.tf`またはワークフローファイル自体の変更をトリガーに、以下3ジョブが実行されます(S3/CloudFront側の`.tf`はこのワークフローの対象外で、現状ローカルから手動apply運用)。

| ジョブ名            | 実行タイミング    | 内容                                                                                                    |
| ------------------- | ----------------- | ------------------------------------------------------------------------------------------------------- |
| **terraform-plan**  | pull request時    | `WordPress`配下で`terraform init` → `fmt -check` → `validate` → `plan`                                  |
| **terraform-apply** | mainへのpush時    | `terraform apply -auto-approve`。作成したEC2のインスタンスIDとS3バケット名を後続ジョブへ出力            |
| **ansible-build**   | terraform-apply後 | ランナーにAnsible・SSMプラグインをインストールし、動的インベントリを作成してEC2へAnsible Playbookを実行 |

## Ansible Playbook 概要

| Role名        | 実行内容                                                                                          |
| ------------- | ------------------------------------------------------------------------------------------------- |
| **apache**    | Apacheインストール、SELinux無効化、`AllowOverride All`の付与(後述)                                |
| **php**       | PHP・PHP-FPM・`php-gd`インストール、アップロード上限の緩和                                        |
| **mysql**     | MySQL 8.0インストール、rootパスワード変更(再実行時はスキップ)、WordPress用DB/ユーザー作成         |
| **wordpress** | WordPress本体のダウンロード・展開・`wp-config.php`設定、Cocoon親子テーマをtarでまとめて転送・展開 |

## リポジトリ構成

```bash
.
├── .github/workflows/
│   └── terraform.yaml          # WordPress用CI/CD (plan/apply/ansible-build)
├── functions/
│   └── rewrite-index.js        # CloudFront Function (index.html自動補完)
├── WordPress/
│   ├── main.tf                 # VPC/EC2/IAM/S3(tfstate用途とは別のアプリ用バケット)
│   ├── themes/
│   │   ├── cocoon-master/      # Cocoon親テーマ
│   │   └── cocoon-child/       # 自作子テーマ
│   └── ansible/
│       ├── playbook.yaml
│       └── roles/
│           ├── apache/
│           ├── php/
│           ├── mysql/
│           └── wordpress/
├── main.tf / provider.tf       # S3+CloudFront側のTerraform (provider, required_providers)
├── s3.tf                       # 静的サイト用S3バケット + バケットポリシー
├── cloudfront.tf               # CloudFront Distribution / OAC / CloudFront Function / ACM
├── variables.tf / terraform.tfvars
├── outputs.tf
└── README.md
```

## 工夫した点

- **OIDC + SSMを用いた認証方式**
  アクセスキーやSSHキーペアを一切使わず、GitHub ActionsはOIDCでAWSロールを引き受け、AnsibleはSSM Session Manager経由でEC2に接続する構成にしました。22番ポートを開ける必要がありません。

- **Ansibleのファイル転送方式の見直し**
  Cocoon親テーマは1,800ファイル超あり、Ansibleの`copy`モジュールでSSM経由に1ファイルずつ転送すると数時間かかる見込みでした。制御ノード側で`tar`にまとめて1ファイルとして転送し、リモートで`unarchive`展開する方式に変更した結果、CI/CD全体が約8分で完了するようになりました。

## 課題と解決

**MySQLセットアップタスクの非冪等性**

- **課題**: EC2を壊さずAnsibleだけ再実行すると、rootパスワードの初期化タスクが「一時パスワードの失効」により2回目以降失敗するようになった。
- **対応**: 本来のrootパスワードで疎通確認できるかを先にチェックし、まだ変更されていない場合のみ一時パスワードでの変更処理を実行するようガードを追加した。

**EC2再起動によるパブリックIP変動とWordPress内部URLの不整合**

- **課題**: インスタンスタイプ変更(t3.micro→t3.small)のため再起動すると、パブリックIPが変わり管理画面にログインできなくなった。
- **対応**: 原因はWordPressのDB(`wp_options`の`siteurl`/`home`)に古いIPが保存されたままだったこと。Elastic IP導入も検討したが、停止中も課金され続けるコスト特性を踏まえて今回は見送り、DBの値を現在のIPに修正する対応とした。

## 今後の改善点

- Ansible実行結果やインフラ変更のSlack通知など、運用面の可視化を強化する
- WordPress・MySQLのバックアップ(スナップショット/DBダンプ)を定期実行する仕組みを整える
- S3/CloudFront側のTerraformもGitHub Actions経由でのCI/CDに乗せ、手動apply運用から卒業する
- EC2誤destroy防止(`disable_api_termination`・`prevent_destroy`)を、スナップショット取得と連動した運用フローとして整理する
