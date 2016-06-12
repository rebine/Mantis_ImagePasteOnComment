# Mantis_ImagePasteOnComment
Mantis Plugin ImagePasteOnComment develop.

# Requirements
Mantis bt 1.3.0 higher.

This plugin require high priority than 'MantisBT Core'.
![priority](Screenshots/ImagePasteOnComment.sc01.png)

# Usage
Write comment.

%[file_id],rate100

file_id is below example.

http://example.com/mantis/file_download.php?file_id=1&type=bug
![file id](Screenshots/ImagePasteOnComment.sc02.png)

rate100 is 100% width at comment field.

# 説明
英語は下手なので日本語でざっくりと説明すると、
アップロードした画像をコメントの部分に張り込めるプラグインです。
1.2.0系では「EVENT_VIEW_BUG_ATTACHMENT」のイベントがなかったので、1.3.0系にしました。
branchをmantisbt12に分けています。

MantisBTのアップロードされた画像は固有のIDがついているので
それを%[file_id]の部分に書き込んでもらえれば、プレビュー画像を
コメントの幅に併せて表示してくれます。

rate100っていうのは、幅100％です。縦横比率はアップロードされたままを
保持しますので、高さを変えたいという人は他のプラグインを探してください。
（幅さえ合っていれば、細かい調整をしたくないというずぼらな人間が作っています)

rate10,rate30,rate70,rate100,rate150など、変化させる値も大体こういう数字と
決めておいたほうが楽だと思います。

あと、プラグインはMantisBT Coreを拡張しているので必要なのですが、
優先度が同じだと表示されません。このプラグインの優先度の数字を
MantisBT Coreよりも小さくしてください。

# 改良予定
- 基本機能のプレビュー画像はクリックするとダウンロードですが、
　独自のパッチでコメントフィールドに%[file_id],rate100を挿入するように
　変更してしまいました。プラグインでどのようにしたらよいのかわからないので
　わかり次第加えたいと思います。

# 募集
- 説明の素敵な英訳
- スタイルシートの素敵なサンプル

# スクリーンショットに出ている画像について
https://jp.fotolia.com/id/51565496
作者：christine krahl
fotoliaにてXS画像を購入して使いました。
