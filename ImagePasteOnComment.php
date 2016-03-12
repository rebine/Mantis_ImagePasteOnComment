<?php
class ImagePasteOnCommentPlugin extends MantisPlugin {
    function register() {
        $this->name = 'ImagePasteOnComment';    # プラグインの名称
        $this->description = 'You can view Image file on Comments';    # プラグインの詳細
        $this->page = '';         # プラグインの初期ページ

        $this->version = '1.0';     # プラグインのバージョン
        $this->requires = array(    # 必要なMantisCoreのバージョン
            'MantisCore' => '1.2.0',
            );

        $this->author = 'Ryuji Ebine';         # 作成者
        $this->contact = 'rebine@redalarm.jp';        # 作成者のアドレス
        $this->url = 'https://github.com/rebine/Mantis_ImagePasteOnComment';            # サポートページ
    }
}
