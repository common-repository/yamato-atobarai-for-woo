<?php

class KUATOWCCodes {

	public static $StatusInfos
		= array(
			//'1'  => '承認済み',
			'1'  => '与信',
			'2'  => '取消',
			//'3'  => '送り状番号登録済み',
			'3'  => '売上確定',
			'5'  => '配送要調査',
			'10' => '売上確定',
			//'11' => '請求書発行済み',
			'11' => '売上確定',
			//'12' => '入金済み',
			'12' => '売上確定',
			'30' => '審査中',
			'31' => '取消',
			'32' => '決済不可',
			//独自取引情報
			'33' => '金額変更',
			'34' => '金額変更ＮＧ',
		);

	public static $ResultErrorFront
		= array(
			'1' => 'サイト管理者にお問い合わせください。',
			'2' => '限度額が超過しています。',
		);

	public static $ResultErrorAdmin
		= array(
			'1' => 'ご利用不可。',
			'2' => '限度額超過',
		);

	public static $WarningStatus
		= array(
			'0' => '警報なし',
			'1' => '送り状番号未登録',
			'2' => '商品配達未完了',
			'3' => '商品配達状況要調査',
			'4' => '請求書印字データ未 DL',
		);


	public static $ErrorCodes
		= array(
			'kaara002E'  => array( 'field' => '加盟店コード', 'label' => '指定桁数(11桁)であること。' ),
			'kaara003E'  => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaara004E'  => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaara006E'  => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaara007E'  => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。 半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。'
			),
			'kaara008E'  => array( 'field' => '受注番号', 'label' => '指定桁数(20桁)以内であること。' ),
			'kaara009E'  => array( 'field' => '受注番号', 'label' => '加盟店様の全決済において一意(ユニーク)であること。' ),
			'kaara010E'  => array( 'field' => '受注日', 'label' => '値が指定されていること。' ),
			'kaara011E'  => array( 'field' => '受注日', 'label' => '半角数字であること。' ),
			'kaara012E'  => array( 'field' => '受注日', 'label' => '指定桁数(8桁)であること。' ),
			'kaara013E'  => array( 'field' => '受注日', 'label' => '日付として妥当であること。[西暦年月日、yyyyMMdd形式]' ),
			'kaara014E'  => array( 'field' => '出荷予定日', 'label' => '値が指定されていること。' ),
			'kaara015E'  => array( 'field' => '出荷予定日', 'label' => '半角数字であること。' ),
			'kaara016E'  => array( 'field' => '出荷予定日', 'label' => '指定桁数(8桁)であること。' ),
			'kaara017E'  => array( 'field' => '出荷予定日', 'label' => '日付として妥当であること。[西暦年月日、yyyyMMdd形式]' ),
			'kaara018E'  => array( 'field' => '出荷予定日', 'label' => '決済を依頼された日付を起算日として、定められた期間内の日付であること。[既定期間:90日間]' ),
			'kaara019E'  => array( 'field' => '氏名', 'label' => '値が指定されていること。' ),
			'kaara020E'  => array( 'field' => '氏名', 'label' => '全角文字であること。' ),
			'kaara021E'  => array( 'field' => '氏名', 'label' => '指定桁数(30桁)以内であること。' ),
			'kaara022E'  => array( 'field' => '氏名', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaara023E'  => array( 'field' => '氏名カナ', 'label' => '半角カナであること。' ),
			'kaara024E'  => array( 'field' => '氏名カナ', 'label' => '指定桁数(80桁)以内であること。' ),
			'kaara025E'  => array( 'field' => '郵便番号', 'label' => '値が指定されていること。' ),
			'kaara026E'  => array( 'field' => '郵便番号', 'label' => '半角数字または”-”[ハイフン]の組み合わせであること。' ),
			'kaara027E'  => array( 'field' => '郵便番号', 'label' => '”-”[ハイフン]を含む場合、1つのみ含まれていること。' ),
			'kaara028E'  => array( 'field' => '郵便番号', 'label' => '”-”[ハイフン]を除いた半角数字が、指定桁数(7桁)であること。' ),
			'kaara029E'  => array( 'field' => '郵便番号', 'label' => '実在する郵便番号であること。' ),
			'kaara030E'  => array( 'field' => '住所１', 'label' => '値が指定されていること。' ),
			'kaara031E'  => array( 'field' => '住所１', 'label' => '全角文字であること。' ),
			'kaara032E'  => array( 'field' => '住所１', 'label' => '指定桁数(25桁)以内であること。' ),
			'kaara033E'  => array( 'field' => '住所１', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaara034E'  => array( 'field' => '住所１', 'label' => '都道府県から始まる住所であること。' ),
			'kaara035E'  => array( 'field' => '住所２', 'label' => '全角文字であること。' ),
			'kaara036E'  => array( 'field' => '住所２', 'label' => '指定桁数(25桁)以内であること。' ),
			'kaara037E'  => array( 'field' => '住所２', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaara038E'  => array( 'field' => '電話番号', 'label' => '値が指定されていること。' ),
			'kaara039E'  => array( 'field' => '電話番号', 'label' => '半角数字または”-”[ハイフン]の組み合わせであること。' ),
			'kaara040E'  => array(
				'field' => '電話番号',
				'label' => '(”-”[ハイフン]を除いた状態で)以下の指定桁数であること。 携帯電話番号の場合、指定桁数(11桁)であること。 その他電話番号の場合、指定桁数(10桁)であること。'
			),
			'kaara043E'  => array( 'field' => 'メールアドレス', 'label' => '半角英数字であること。' ),
			'kaara044E'  => array( 'field' => 'メールアドレス', 'label' => '指定桁数(64桁)以内であること。' ),
			'kaara045E'  => array(
				'field' => 'メールアドレス',
				'label' => 'メールアドレスの構成として、ローカル部とドメイン部の間に最低１つの”@”[アットマーク]が含まれること。補足） ローカル部@ドメイン部 (例: sample@kuronekoyamato.co.jp )'
			),
			'kaara095E'  => array( 'field' => 'フリー項目', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaara095E ' => array( 'field' => 'フリー項目', 'label' => '全角漢字、平仮名、カタカナ、英数字、‐、＿又は半角英数字、ｶﾀｶﾅ、‐、_であること。' ),
			'kaara096E'  => array( 'field' => 'フリー項目全角指定桁数', 'label' => '（20桁）以内又は半角指定桁数（40桁）以内であること。' ),
			'kaara046E'  => array( 'field' => '決済金額総計', 'label' => '値が指定されていること。' ),
			'kaara047E'  => array( 'field' => '決済金額総計', 'label' => '半角数字であること。' ),
			'kaara048E'  => array( 'field' => '決済金額総計', 'label' => '指定桁数(6桁)以内であること' ),
			'kaara049E'  => array( 'field' => '決済金額総計', 'label' => '1円以上、999999円以内の金額であること。' ),
			'kaara050E'  => array( 'field' => '送り先区分', 'label' => '値が指定されていること。' ),
			'kaara051E'  => array( 'field' => '送り先区分', 'label' => '送り先区分半角数字であること。' ),
			'kaara052E'  => array(
				'field' => '送り先区分',
				'label' => '“同梱”を利用されない加盟店様の場合、下記のコードのいずれかであること。0:別送・本人送り 1:別送・本人送り以外(ギフト等)'
			),
			'kaara053E'  => array(
				'field' => '送り先区分',
				'label' => '“同梱”を利用される加盟店様の場合、下記のコードのいずれかであること。 0:別送・本人送り 1:別送・本人送り以外(ギフト等) 2:同梱'
			),
			'kaara054E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '全角文字であること。' ),
			'kaara055E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '指定桁数(30 桁)以内であること。' ),
			'kaara056E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaara057E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '購入商品名称(１)が指定されていること。' ),
			'kaara058E'  => array( 'field' => '購入商品数量(1～10)', 'label' => '半角数字であること。' ),
			'kaara059E'  => array( 'field' => '購入商品数量(1～10)', 'label' => '指定桁数(4 桁)以内であること。' ),
			'kaara060E'  => array( 'field' => '購入商品単価(1～10)', 'label' => '半角数字または”-”[マイナス]の組み合わせであること。' ),
			'kaara061E'  => array(
				'field' => '購入商品単価(1～10)',
				'label' => '正数の場合、指定桁数(6 桁)以内であること。 [ 最小値＝0 最大値＝999999 ]'
			),
			'kaara062E'  => array(
				'field' => '購入商品単価(1～10)',
				'label' => '負数の場合、指定桁数(7 桁)以内であること。 [ 最小値＝-999999 最大値＝-1 ]'
			),
			'kaara063E'  => array( 'field' => '購入商品小計(1～10)', 'label' => '半角数字または”-”[マイナス]の組み合わせであること。' ),
			'kaara064E'  => array(
				'field' => '購入商品小計(1～10)',
				'label' => '正数の場合、指定桁数(6 桁)以内であること。 [ 最小値＝0 最大値＝999999 ]'
			),
			'kaara065E'  => array(
				'field' => '購入商品小計(1～10)',
				'label' => '負数の場合、指定桁数(7 桁)以内であること。 [ 最小値＝-999999 最大値＝-1 ]'
			),
			'kaara066E'  => array( 'field' => '送り先名称', 'label' => '全角文字であること。' ),
			'kaara067E'  => array( 'field' => '送り先名称', 'label' => '指定桁数(30 桁)以内であること。' ),
			'kaara068E'  => array( 'field' => '送り先名称', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaara070E'  => array( 'field' => '送り先郵便番号', 'label' => '半角数字または”-”[ハイフン]の組み合わせであること。' ),
			'kaara071E'  => array( 'field' => '送り先郵便番号', 'label' => '”-”[ハイフン]を含む場合、1 つ含まれていること。' ),
			'kaara072E'  => array( 'field' => '送り先郵便番号', 'label' => '”-”[ハイフン]を除いた半角数字が、指定桁数(7 桁)であること。' ),
			'kaara073E'  => array( 'field' => '送り先郵便番号', 'label' => '実在する郵便番号であること。' ),
			'kaara075E'  => array( 'field' => '送り先住所１', 'label' => '全角文字であること。' ),
			'kaara076E'  => array( 'field' => '送り先住所１', 'label' => '指定桁数(25 桁)以内であること。' ),
			'kaara077E'  => array( 'field' => '送り先住所１', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaara078E'  => array( 'field' => '送り先住所１', 'label' => '都道府県から始まる住所であること。' ),
			'kaara079E'  => array( 'field' => '送り先住所２', 'label' => '全角文字であること。' ),
			'kaara080E'  => array( 'field' => '送り先住所２', 'label' => '指定桁数(25 桁)以内であること。' ),
			'kaara081E'  => array( 'field' => '送り先住所２', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaara082E'  => array( 'field' => '送り先電話番号', 'label' => '半角数字または”-”[ハイフン]の組み合わせであること。' ),
			'kaara083E'  => array( 'field' => '送り先電話番号', 'label' => '”-”[ハイフン]を含む場合、2 つ含まれていること。' ),
			'kaara084E'  => array(
				'field' => '送り先電話番号',
				'label' => '(”-”[ハイフン]を除いた状態で)以下の指定桁数であること。携帯電話番号の場合、指定桁数(11 桁)であること。その他電話番号の場合、指定桁数(10 桁)であること。'
			),
			'kaara086E'  => array( 'field' => '依頼日時', 'label' => '値が指定されていること。' ),
			'kaara087E'  => array( 'field' => '依頼日時', 'label' => '半角数字であること。' ),
			'kaara088E'  => array( 'field' => '依頼日時', 'label' => '指定桁数(14 桁)であること。' ),
			'kaara089E'  => array( 'field' => '依頼日時', 'label' => '日付及び時刻として妥当であること。 [西暦年月日＋時分秒、yyyyMMddHHmmss 形式]' ),
			'kaara090E'  => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaara091E'  => array( 'field' => 'パスワード', 'label' => '半角英数字であること。' ),
			'kaara092E'  => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaara093E'  => array( 'field' => '電話番号', 'label' => '”0”[ゼロ]で始まる電話番号であること。' ),
			'kaara094E'  => array( 'field' => '送り先電話番号', 'label' => '”0”[ゼロ]で始まる電話番号であること。' ),
			'kaara100E'  => array( 'field' => '関連チェック１', 'label' => '出荷予定日は受注日以降の日付であること。' ),
			'kaara101E'  => array( 'field' => '関連チェック２', 'label' => '決済金額総計と、購入商品小計(最小 1 件、最大 10 件)の合計金額が一致すること。 ' ),
			'kaara102E'  => array(
				'field' => '関連チェック３',
				'label' => '送り先区分が“1:別送・本人送り以外(ギフト等)”の場合、送り先名称、送り先郵便番号、送り先住所１が指定されていること。'
			),
			'kaara103E'  => array( 'field' => '関連チェック４', 'label' => '住所１と住所２を結合して１つの住所とした状態で“番地”が指定されていること。' ),
			'kaara104E'  => array( 'field' => '関連チェック５', 'label' => '送り先住所１と送り先住所２を結合して１つの住所とした状態で“番地”が指定されていること。' ),
			'kaara105E'  => array( 'field' => '郵便番号', 'label' => '”-”[ハイフン]が入力されていた場合、郵便番号の形式が、000-0000 形式であること。' ),
			'kaara106E'  => array( 'field' => '送り先郵便番号', 'label' => '”-”[ハイフン]が入力されていた場合、郵便番号の形式が、000-0000 形式であること。' ),
			'kaars001E'  => array( 'field' => '項目チェック', 'label' => '指定されたレイアウトであること。' ),
			'kaars002E'  => array( 'field' => '加盟店コード', 'label' => '指定桁数(11 桁)であること。' ),
			'kaars003E'  => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaars004E'  => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaars005E'  => array( 'field' => '受注番号', 'label' => '指定桁数(20 桁)以内であること。' ),
			'kaars006E'  => array( 'field' => '受注番号', 'label' => '半角文字であること。' ),
			'kaars007E'  => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaars008E'  => array( 'field' => '受注番号', 'label' => '取消済でない存在する受注番号であること。' ),
			'kaars009E'  => array( 'field' => '依頼日時', 'label' => '日付及び時刻として妥当であること。 [西暦年月日＋時分秒、yyyyMMddHHmmss 形式]' ),
			'kaars010E'  => array( 'field' => '依頼日時', 'label' => '半角数字であること。' ),
			'kaars011E'  => array( 'field' => '依頼日時', 'label' => '値が指定されていること。' ),
			'kaars012E'  => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaars013E'  => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaars014E'  => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。 半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。'
			),
			'kaacl001E'  => array( 'field' => '項目チェック', 'label' => '指定されたレイアウトであること。' ),
			'kaacl002E'  => array( 'field' => '加盟店コード', 'label' => '指定桁数(11 桁)であること。' ),
			'kaacl003E'  => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaacl004E'  => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaacl005E'  => array( 'field' => '受注番号', 'label' => '指定桁数(20 桁)以内であること。' ),
			'kaacl006E'  => array( 'field' => '受注番号', 'label' => '半角であること。' ),
			'kaacl007E'  => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaacl008E'  => array( 'field' => '受注番号', 'label' => '存在する受注番号であること。' ),
			'kaacl009E'  => array( 'field' => '受注番号', 'label' => '取消可能なお取引状況であること。 ' ),
			'kaacl010E'  => array( 'field' => '依頼日時', 'label' => '日付及び時刻として妥当であること。 [西暦年月日＋時分秒、yyyyMMddHHmmss 形式]' ),
			'kaacl011E'  => array( 'field' => '依頼日時', 'label' => '半角数字であること。' ),
			'kaacl012E'  => array( 'field' => '依頼日時', 'label' => '値が指定されていること。' ),
			'kaacl013E'  => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaacl014E'  => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaacl015E'  => array( 'field' => '受注番号', 'label' => '入金済みのお取引状況でないこと。' ),
			'kaacl016E'  => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。 半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。'
			),
			'kaacl017E'  => array( 'field' => '加盟店コード', 'label' => 'ご利用可能な加盟店様の加盟店コードであること。' ),
			'kaasl001E'  => array( 'field' => '項目チェック', 'label' => '指定されたレイアウトであること。' ),
			'kaasl002E'  => array( 'field' => '加盟店コード', 'label' => '指定桁数(11 桁)であること。' ),
			'kaasl003E'  => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaasl004E'  => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaasl005E'  => array( 'field' => '受注番号', 'label' => '指定桁数(20 桁)以内であること。' ),
			'kaasl006E'  => array( 'field' => '受注番号', 'label' => '半角であること。' ),
			'kaasl007E'  => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaasl008E'  => array( 'field' => '受注番号', 'label' => '存在する受注番号であること。' ),
			'kaasl010E'  => array(
				'field' => '関連チェック１',
				'label' => '指定桁数(12 桁)以内であること。 (処理区分“0:新規登録または変更登録”、処理区分“1:変更登録”)'
			),
			'kaasl011E'  => array(
				'field' => '関連チェック１',
				'label' => '送り状番号の値が指定されていること。 (処理区分“0:新規登録または変更登録”、処理区分“1:変更登録”)'
			),
			'kaasl012E'  => array(
				'field' => '関連チェック１',
				'label' => '半角英数字または“-”（ハイフン）であること。 (処理区分“0:新規登録または変更登録”、処理区分“1:変更登録”)'
			),
			'kaasl013E'  => array( 'field' => '関連チェック１', 'label' => '値が指定されていないこと。 (処理区分“2:取消”)' ),
			'kaasl014E'  => array(
				'field' => '関連チェック１',
				'label' => '送り状番号のチェックデジットにおいて番号体系が妥当であること。 (処理区分“0:新規登録”、処理区分“1:変更登録”)'
			),
			'kaasl015E'  => array(
				'field' => '関連チェック１',
				'label' => '代金後払いでご利用可能な送り状番号であること。(処理区分“0:新規登録または変更登録”、処理区分“1:変更登録”)'
			),
			'kaasl019E'  => array( 'field' => '処理区分', 'label' => '下記のコードのいずれかが指定されていること。 0:新規登録または変更登録 1:変更登録 9:取消 ' ),
			'kaasl020E'  => array( 'field' => '処理区分', 'label' => '“0:新規登録または変更登録”が可能なお取引状況であること。' ),
			'kaasl022E'  => array( 'field' => '処理区分', 'label' => '“0:新規登録または変更登録”“1:変更登録”および“9:取消”が可能なお取引状況であること。' ),
			'kaasl023E'  => array( 'field' => '関連チェック 2', 'label' => '存在する日付であること。' ),
			'kaasl027E'  => array( 'field' => '関連チェック 2', 'label' => '出荷予定日は受注日以降の日付であること。 (処理区分“1:変更登録”)' ),
			'kaasl031E'  => array( 'field' => '関連チェック２', 'label' => '値が指定されていないこと。 (処理区分“9:取消”)' ),
			'kaasl033E'  => array( 'field' => '依頼日時', 'label' => '日付及び時刻として妥当であること。 [西暦年月日＋時分秒、yyyyMMddHHmmss 形式]' ),
			'kaasl034E'  => array( 'field' => '依頼日時', 'label' => '半角数字であること。' ),
			'kaasl035E'  => array( 'field' => '依頼日時', 'label' => '値が指定されていること。' ),
			'kaasl036E'  => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaasl037E'  => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaasl038E'  => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。 半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。'
			),
			'kaasl041E'  => array( 'field' => '加盟店コード', 'label' => '存在する加盟店コードであること。' ),
			'kaasl042E'  => array(
				'field' => '関連チェック 2',
				'label' => '決済を依頼された日付を起算日として、定められた期間内の日付であること。[既定期間:90 日間]'
			),
			'kaasl044E'  => array( 'field' => '関連チェック１', 'label' => '半角英数字であること。' ),
			'kaasl045E'  => array( 'field' => '関連チェック１', 'label' => '同一送り状番号の登録されている受注番号が 3 件以内であること。' ),
			'kaast001E'  => array( 'field' => '項目チェック', 'label' => '指定されたレイアウトであること。' ),
			'kaast002E'  => array( 'field' => '加盟店コード', 'label' => '指定桁数(11 桁)であること。' ),
			'kaast003E'  => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaast004E'  => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaast005E'  => array( 'field' => '受注番号', 'label' => '指定桁数(20 桁)以内であること。' ),
			'kaast006E'  => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。 半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。'
			),
			'kaast007E'  => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaast008E'  => array( 'field' => '受注番号', 'label' => '存在する受注番号であること。' ),
			'kaast009E'  => array( 'field' => '依頼日時', 'label' => '日付及び時刻として妥当であること。 [西暦年月日＋時分秒、yyyyMMddHHmmss 形式]' ),
			'kaast010E'  => array( 'field' => '依頼日時', 'label' => '半角数字であること。' ),
			'kaast011E'  => array( 'field' => '依頼日時', 'label' => '値が指定されていること。' ),
			'kaast012E'  => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaast013E'  => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaast014E'  => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。 半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。'
			),
			'kaast015E'  => array( 'field' => '加盟店コード', 'label' => 'ご利用可能な加盟店様の加盟店コードであること。' ),
			'kaasd001E'  => array( 'field' => '項目チェック', 'label' => '指定されたレイアウトであること。' ),
			'kaasd002E'  => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaasd003E'  => array( 'field' => '加盟店コード', 'label' => '指定桁数(11 桁)であること。' ),
			'kaasd004E'  => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaasd005E'  => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaasd006E'  => array( 'field' => '受注番号', 'label' => '指定桁数(20 桁)以内であること。' ),
			'kaasd007E'  => array( 'field' => '受注番号', 'label' => '半角であること。' ),
			'kaasd008E'  => array( 'field' => '受注番号', 'label' => '存在する受注番号であること。' ),
			'kaasd009E'  => array( 'field' => '受注番号', 'label' => '請求書印字情報を取得可能なお取引状況であること。' ),
			'kaasd018E'  => array( 'field' => '受注番号', 'label' => '請求書印字情報を取得可能なお取引状況であること。' ),
			'kaasd010E'  => array( 'field' => '受注番号', 'label' => '指定された受注番号が“同梱”であること。' ),
			'kaasd011E'  => array( 'field' => '依頼日時', 'label' => '値が指定されていること。' ),
			'kaasd012E'  => array( 'field' => '依頼日時', 'label' => '半角数字であること。' ),
			'kaasd013E'  => array( 'field' => '依頼日時', 'label' => '日付及び時刻として妥当であること。 [西暦年月日＋時分秒、yyyyMMddHHmmss 形式]' ),
			'kaasd014E'  => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaasd015E'  => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaasd016E'  => array( 'field' => '受注番号', 'label' => '半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]の組み合わせであること。' ),
			'kaasd019E'  => array( 'field' => '加盟店コード', 'label' => 'ご利用可能な加盟店様の加盟店コードであること。' ),
			'kaakk001E'  => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaakk002E'  => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaakk003E'  => array( 'field' => '加盟店コード', 'label' => '指定桁数(11 桁)であること。' ),
			'kaakk004E'  => array( 'field' => '加盟店コード', 'label' => '登録されている加盟店であること。' ),
			'kaakk005E'  => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaakk006E'  => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。 半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。 始まりの番号と終わりの番号が”-”[ハイフン]または”_”[アンダライン]以外の英数字であること。'
			),
			'kaakk007E'  => array( 'field' => '受注番号', 'label' => '指定桁数(20 桁)以内であること。' ),
			'kaakk008E'  => array( 'field' => '受注番号', 'label' => '存在する受注番号であること。' ),
			'kaakk009E'  => array( 'field' => '受注番号', 'label' => '請求金額変更（減額）可能な審査結果であること。明細の合計額と利用金額が一致していること。' ),
			'kaakk010E'  => array( 'field' => '受注番号', 'label' => '請求金額変更（減額）可能なお取引状況であること。 ※外部連携対象外の決済情報であること。' ),
			'kaakk011E'  => array( 'field' => '受注番号', 'label' => '請求金額変更（減額）可能なお取引状況であること。' ),
			'kaakk013E'  => array( 'field' => '出荷予定日', 'label' => '半角数字であること。' ),
			'kaakk014E'  => array( 'field' => '出荷予定日', 'label' => '指定桁数(8 桁)であること。' ),
			'kaakk015E'  => array( 'field' => '出荷予定日', 'label' => '日付として妥当であること。 [西暦年月日、yyyyMMdd 形式]' ),
			'kaakk016E'  => array( 'field' => '出荷予定日', 'label' => '受注日以降の日付であること。 [西暦年月日、yyyyMMdd 形式]' ),
			'kaakk017E'  => array( 'field' => '出荷予定日', 'label' => '決済を依頼された日付を起算日として、定められた期間内の日付であること。[既定期間:90 日間]' ),
			'kaakk018E'  => array( 'field' => '郵便番号', 'label' => '値が指定されていること。' ),
			'kaakk019E'  => array( 'field' => '郵便番号', 'label' => '半角数字または”-”[ハイフン]の組み合わせであること。' ),
			'kaakk020E'  => array( 'field' => '郵便番号', 'label' => '”-”[ハイフン]を除いた半角数字が、指定桁数(7 桁)であること。' ),
			'kaakk021E'  => array( 'field' => '郵便番号', 'label' => '”-”[ハイフン]を含む場合、1 つのみ含まれていること。' ),
			'kaakk022E'  => array( 'field' => '郵便番号', 'label' => '実在する郵便番号であること。' ),
			'kaakk023E'  => array( 'field' => '住所１', 'label' => '値が指定されていること。' ),
			'kaakk024E'  => array( 'field' => '住所１', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaakk025E'  => array( 'field' => '住所１', 'label' => '全角文字であること。' ),
			'kaakk026E'  => array( 'field' => '住所１', 'label' => '指定桁数(25 桁)以内であること。' ),
			'kaakk027E'  => array( 'field' => '住所１', 'label' => '都道府県から始まる住所であること。' ),
			'kaakk028E'  => array( 'field' => '住所２', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaakk029E'  => array( 'field' => '住所２', 'label' => '全角文字であること。' ),
			'kaakk030E'  => array( 'field' => '住所２', 'label' => '指定桁数(25 桁)以内であること。' ),
			'kaakk031E'  => array( 'field' => '住所２', 'label' => '住所１と住所２を結合して１つの住所とした状態で“番地”が指定されていること。' ),
			'kaakk032E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '購入商品名称(１)が指定されていること。' ),
			'kaakk033E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaakk034E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '全角文字であること。' ),
			'kaakk035E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '指定桁数(30 桁)以内であること。' ),
			'kaakk036E'  => array( 'field' => '購入商品数量(1～10)', 'label' => '半角数字であること。' ),
			'kaakk037E'  => array( 'field' => '購入商品数量(1～10)', 'label' => '指定桁数(4 桁)以内であること。' ),
			'kaakk038E'  => array( 'field' => '購入商品単価(1～10)', 'label' => '半角数字または”-”[マイナス]の組み合わせであること。' ),
			'kaakk039E'  => array( 'field' => '購入商品単価(1～10)', 'label' => '負数の場合、”-”[マイナス]が先頭に設定されている単価であること。' ),
			'kaakk040E'  => array(
				'field' => '購入商品単価(1～10)',
				'label' => '正数の場合、指定桁数(6 桁)以内であること。 [ 最小値＝0 最大値＝999999 ]'
			),
			'kaakk041E'  => array(
				'field' => '購入商品単価(1～10)',
				'label' => '負数の場合、指定桁数(7 桁)以内であること。 [ 最小値＝-999999 最大値＝-1 ]'
			),
			'kaakk042E'  => array( 'field' => '購入商品小計(1～10)', 'label' => '半角数字または”-”[マイナス]の組み合わせであること。' ),
			'kaakk043E'  => array( 'field' => '購入商品小計(1～10)', 'label' => '負数の場合、”-”[マイナス]が先頭に設定されている小計であること。' ),
			'kaakk044E'  => array(
				'field' => '購入商品小計(1～10)',
				'label' => '正数の場合、指定桁数(6 桁)以内であること。 [ 最小値＝0 最大値＝999999 ]'
			),
			'kaakk045E'  => array(
				'field' => '購入商品小計(1～10)',
				'label' => '負数の場合、指定桁数(7 桁)以内であること。 [ 最小値＝-999999 最大値＝-1]'
			),
			'kaakk046E'  => array( 'field' => '決済金額総計', 'label' => '値が指定されていること。' ),
			'kaakk047E'  => array( 'field' => '決済金額総計', 'label' => '半角数字であること。' ),
			'kaakk048E'  => array( 'field' => '決済金額総計', 'label' => '指定桁数(6 桁)以内であること。' ),
			'kaakk049E'  => array( 'field' => '決済金額総計', 'label' => '変更前の決済金額総計と相違した値であること。' ),
			'kaakk050E'  => array( 'field' => '決済金額総計', 'label' => '初回の決済金額総計より減額した値であること。' ),
			'kaakk051E'  => array( 'field' => '決済金額総計', 'label' => '決済金額総計に購入商品小計の合計値が指定されていること。' ),
			'kaakk053E'  => array( 'field' => '送り先区分', 'label' => '半角数字であること。' ),
			'kaakk054E'  => array( 'field' => '送り先区分', 'label' => '下記のコードのいずれかであること。 0:別送 1:同梱' ),
			'kaakk055E'  => array( 'field' => '送り先区分', 'label' => '“同梱”を利用されない加盟店様の場合、0:別送が指定されていること。' ),
			'kaakk056E'  => array( 'field' => 'フリー項目', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaakk057E'  => array( 'field' => 'フリー項目', 'label' => '全角漢字、平仮名、カタカナ、英数字、‐、＿又は半角英数字、ｶﾀｶﾅ、‐、_であること。' ),
			'kaakk058E'  => array( 'field' => 'フリー項目', 'label' => '全角指定桁数（20 桁）以内又は半角指定桁数（40 桁）以内であること。' ),
			'kaakk059E'  => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaakk060E'  => array( 'field' => 'パスワード', 'label' => '指定桁数(8 桁)以内であること。' ),
			'kaakk061E'  => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaakk062E'  => array( 'field' => '依頼日時', 'label' => '値が指定されていること。' ),
			'kaakk063E'  => array( 'field' => '依頼日時', 'label' => '半角数字であること。' ),
			'kaakk064E'  => array( 'field' => '依頼日時', 'label' => '指定桁数(14 桁)であること。' ),
			'kaakk065E'  => array( 'field' => '依頼日時', 'label' => '日付及び時刻として妥当であること。 [西暦年月日＋時分秒、yyyyMMddHHmmss 形式]' ),
			'kaarr001E'  => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaarr002E'  => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaarr003E'  => array( 'field' => '加盟店コード', 'label' => '指定桁数(11 桁)であること。' ),
			'kaarr004E'  => array( 'field' => '加盟店コード', 'label' => '当社で既に登録済の加盟店コードであること。' ),
			'kaarr005E'  => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaarr006E'  => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。 半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。'
			),
			'kaarr007E'  => array( 'field' => '受注番号', 'label' => '指定桁数(20 桁)以内であること。' ),
			'kaarr008E'  => array( 'field' => '受注番号', 'label' => '加盟店様の全決済において一意(ユニーク)であること。' ),
			'kaarr009E'  => array( 'field' => '受注番号', 'label' => '請求書再発行回数が 7 回以下であること。' ),
			'kaarr010E'  => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaarr011E'  => array( 'field' => 'パスワード', 'label' => '半角英数字であること。' ),
			'kaarr012E'  => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaarr013E'  => array( 'field' => 'ご依頼内容', 'label' => '値が指定されていること。' ),
			'kaarr014E'  => array( 'field' => 'ご依頼内容', 'label' => '以下の値であること。 1:請求内容変更・請求書再発行 3:請求書再発行取下げ' ),
			'kaarr015E'  => array( 'field' => 'ご依頼内容', 'label' => 'ご依頼内容が 1:請求内容変更・請求書再発行の場合、請求内容変更可能なお取引状況であること。' ),
			'kaarr016E'  => array( 'field' => 'ご依頼内容', 'label' => 'ご依頼内容が 1:請求内容変更・請求書再発行の場合、請求書再発行可能なお取引状況であること。' ),
			'kaarr017E'  => array( 'field' => 'ご依頼内容', 'label' => 'ご依頼内容が 3:請求書再発行取下げの場合、再発行取下げ可能なお取引状況であること。' ),
			'kaarr018E'  => array( 'field' => '不備事由', 'label' => 'ご依頼内容が 1:請求内容変更・請求書再発行の場合に値が指定されていること。' ),
			'kaarr019E'  => array( 'field' => '不備事由', 'label' => '以下の値であること。 1:宛先不備 2:紛失 3:破損・汚損 4:転居 5:入力間違い 6:その他' ),
			'kaarr020E'  => array( 'field' => '不備事由その他', 'label' => '不備事由が 6:その他の場合に値が指定されていること。' ),
			'kaarr021E'  => array( 'field' => '不備事由その他', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaarr022E'  => array( 'field' => '不備事由その他', 'label' => '全角文字であること。' ),
			'kaarr023E'  => array( 'field' => '不備事由その他', 'label' => '指定桁数(25 桁)以内であること。' ),
			'kaarr025E'  => array( 'field' => '出荷予定日', 'label' => '半角数字であること。' ),
			'kaarr026E'  => array( 'field' => '出荷予定日', 'label' => '指定桁数(8 桁)であること。' ),
			'kaarr027E'  => array( 'field' => '出荷予定日', 'label' => '日付として妥当であること。 [西暦年月日、yyyyMMdd 形式]' ),
			'kaarr028E'  => array( 'field' => '出荷予定日', 'label' => '決済を依頼された日付を起算日として、定められた期間内の日付であること。[既定期間:90 日間]' ),
			'kaarr029E'  => array( 'field' => '出荷予定日', 'label' => '受注日以降の日付であること。' ),
			'kaarr031E'  => array( 'field' => '送り先区分', 'label' => '半角数字であること。' ),
			'kaarr032E'  => array( 'field' => '送り先区分', 'label' => '下記のコードのいずれかであること。0:別送 1:同梱 ' ),
			'kaarr033E'  => array( 'field' => '送り先区分', 'label' => '“同梱”を利用されない加盟店様の場合、0:別送が指定されていること。' ),
			'kaarr034E'  => array( 'field' => '郵便番号', 'label' => '値が指定されていること。' ),
			'kaarr035E'  => array( 'field' => '郵便番号', 'label' => '半角数字または”-”[ハイフン]の組み合わせであること。' ),
			'kaarr036E'  => array( 'field' => '郵便番号', 'label' => '”-”[ハイフン]を除いた半角数字が、指定桁数(7 桁)であること。' ),
			'kaarr037E'  => array( 'field' => '郵便番号', 'label' => '”-”[ハイフン]を含む場合、1 つのみ含まれていること。' ),
			'kaarr038E'  => array( 'field' => '郵便番号', 'label' => '実在する郵便番号であること。' ),
			'kaarr039E'  => array( 'field' => '住所１', 'label' => '値が指定されていること。' ),
			'kaarr040E'  => array( 'field' => '住所１', 'label' => '全角文字であること。' ),
			'kaarr041E'  => array( 'field' => '住所１', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaarr042E'  => array( 'field' => '住所１', 'label' => '指定桁数(25 桁)以内であること。' ),
			'kaarr043E'  => array( 'field' => '住所１', 'label' => '都道府県から始まる住所であること。' ),
			'kaarr044E'  => array( 'field' => '住所２', 'label' => '全角文字であること。' ),
			'kaarr045E'  => array( 'field' => '住所２', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaarr046E'  => array( 'field' => '住所２', 'label' => '指定桁数(25 桁)以内であること。' ),
			'kaarr047E'  => array( 'field' => '住所２', 'label' => '住所１と住所２を結合して１つの住所とした状態で“番地”が指定されていること。' ),
			'kaarr048E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '購入商品名称(１)が指定されていること。' ),
			'kaarr049E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '全角文字であること。' ),
			'kaarr050E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaarr051E'  => array( 'field' => '購入商品名称(1～10)', 'label' => '指定桁数(30 桁)以内であること。' ),
			'kaarr052E'  => array( 'field' => '購入商品数量(1～10)', 'label' => '半角数字であること。' ),
			'kaarr053E'  => array( 'field' => '購入商品数量(1～10)', 'label' => '指定桁数(4 桁)以内であること。' ),
			'kaarr054E'  => array( 'field' => '購入商品単価(1～10)', 'label' => '半角数字または”-”[マイナス]の組み合わせであること。' ),
			'kaarr055E'  => array( 'field' => '購入商品単価(1～10)', 'label' => '負数の場合、”-”[マイナス]が先頭に指定されていること。' ),
			'kaarr056E'  => array(
				'field' => '購入商品単価(1～10)',
				'label' => '正数の場合、指定桁数(6 桁)以内であること。 [ 最小値＝0 最大値＝999999 ]'
			),
			'kaarr057E'  => array(
				'field' => '購入商品単価(1～10)',
				'label' => '負数の場合、指定桁数(7 桁)以内であること。 [ 最小値＝-999999 最大値＝-1 ]'
			),
			'kaarr063E'  => array( 'field' => '購入商品小計(1～10)', 'label' => '半角数字または”-”[マイナス]の組み合わせであること。' ),
			'kaarr064E'  => array( 'field' => '購入商品小計(1～10)', 'label' => '負数の場合、”-”[マイナス]が先頭に指定されていること。' ),
			'kaarr065E'  => array(
				'field' => '購入商品小計(1～10)',
				'label' => '正数の場合、指定桁数(6 桁)以内であること。 [ 最小値＝0 最大値＝999999 ]'
			),
			'kaarr066E'  => array(
				'field' => '購入商品小計(1～10)',
				'label' => '負数の場合、指定桁数(7 桁)以内であること。 [ 最小値＝-999999 最大値＝-1 ]'
			),
			'kaarr058E'  => array( 'field' => 'フリー項目', 'label' => '“同梱”を利用されない加盟店様の場合、空白であること。' ),
			'kaarr059E'  => array( 'field' => 'フリー項目', 'label' => '指定された文字コードの範囲文字であること。' ),
			'kaarr060E'  => array( 'field' => 'フリー項目', 'label' => '全角漢字、平仮名、カタカナ、英数字、‐、＿又は半角英数字、ｶﾀｶﾅ、‐、_であること。' ),
			'kaarr061E'  => array( 'field' => 'フリー項目', 'label' => '全角指定桁数（20桁）以内又は半角指定桁数（40桁）以内であること。' ),
			'kaarr067E'  => array(
				'field' => '関連チェック 1',
				'label' => '既に登録済みの請求金額合計と、購入商品小計(最小 1 件、最大 10 件)の合計金額が一致すること。'
			),

			'kaasa001E' => array( 'field' => '加盟店コード', 'label' => '値が指定されていること。' ),
			'kaasa002E' => array( 'field' => '加盟店コード', 'label' => '半角数字であること。' ),
			'kaasa003E' => array( 'field' => '加盟店コード', 'label' => '指定桁数(11 桁)であること。' ),
			'kaasa004E' => array( 'field' => '加盟店コード', 'label' => '当社で既に登録済の加盟店コードであること。' ),
			'kaasa006E' => array( 'field' => '受注番号', 'label' => '値が指定されていること。' ),
			'kaasa007E' => array(
				'field' => '受注番号',
				'label' => '半角英数字であること。半角の数字(0～9)または英字(a～z,A～Z)または”-”[ハイフン]または”_”[アンダライン]の組み合わせであること。'
			),
			'kaasa010E' => array( 'field' => '認証コード', 'label' => '値が指定されていること。' ),
			'kaasa011E' => array( 'field' => '認証コード', 'label' => '半角数字であること。' ),
			'kaasa012E' => array( 'field' => '認証コード', 'label' => '指定桁数(4 桁)以内であること。' ),
			'kaasa013E' => array( 'field' => '依頼日時', 'label' => '値が指定されていること。' ),
			'kaasa014E' => array( 'field' => '依頼日時', 'label' => '半角数字であること。' ),
			'kaasa015E' => array( 'field' => '依頼日時', 'label' => '指定桁数(14 桁)以内であること。' ),
			'kaasa016E' => array( 'field' => '依頼日時', 'label' => '日付及び時刻として妥当であること。[西暦年月日＋時分秒、yyyyMMddHHmmss 形式]' ),
			'kaasa017E' => array( 'field' => 'パスワード', 'label' => '値が指定されていること。' ),
			'kaasa018E' => array( 'field' => 'パスワード', 'label' => '半角英数字であること。' ),
			'kaasa023E' => array( 'field' => 'パスワード', 'label' => '指定桁数(8 桁)以内であること。' ),
			'kaasa024E' => array( 'field' => 'パスワード', 'label' => 'パスワードが一致すること。' ),
			'kaasa020E' => array( 'field' => '関連チェック', 'label' => '番号認証が必要な与信データが存在すること。' ),
			'kaasa021E' => array( 'field' => '関連チェック', 'label' => '認証コード入力回数が入力上限値を超えていないこと。' ),
			'kaasa022E' => array( 'field' => '関連チェック', 'label' => '有効期限切れ処理中の与信データでないこと。' ),
			'err0001'   => array(
				'field' => '例外エラー１',
				'label' => 'ｸﾛﾈｺ代金後払いシステムにて例外エラーが発生しております。お手数ですが、問い合わせ先までご連絡下さい。'
			),
			'err0002'   => array(
				'field' => '例外エラー２',
				'label' => 'ｸﾛﾈｺ代金後払いシステムにて例外エラーが発生しております。お手数ですが、問い合わせ先までご連絡下さい。'
			),
			'err0003'   => array(
				'field' => '例外エラー３',
				'label' => 'ｸﾛﾈｺ代金後払いシステムにて例外エラーが発生しております。お手数ですが、問い合わせ先までご連絡下さい。'
			),
		);

	public static $SmsResult
		= [
			'1' => '判定 OK',
			'2' => 'コード不一致',
			'3' => '有効期限切れ',
			'4' => '認証 SMS 送信 NG',
			'5' => '認証結果の不正',
		];

	/**
	 * statusのラベルを返す
	 *
	 * @param string $code ステータスコード
	 *
	 * @return mixed|string
	 */
	public static function statusInfoLabel( $code ) {
		return isset( self::$StatusInfos[ $code ] ) ? self::$StatusInfos[ $code ] : '';
	}


	/**
	 * エラーラベル
	 *
	 * @param $code
	 *
	 * @return mixed|string
	 */
	public static function errorLabel( $code ) {

		if ( isset( self::$ErrorCodes[ $code ] ) ) {
			return self::$ErrorCodes[ $code ]['field'] . ':' . self::$ErrorCodes[ $code ]['label'];
		}

		return '';
	}

	/**
	 * sms resultのラベルを返す
	 *
	 * @param string $code ステータスコード
	 *
	 * @return mixed|string
	 */
	public static function smsResultLabel( $code ) {
		if ( empty( $code ) ) {
			return '';
		}

		return isset( self::$SmsResult[ $code ] ) ? self::$SmsResult[ $code ] : '';
	}


}

