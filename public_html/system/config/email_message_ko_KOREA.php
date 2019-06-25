<?php

$config['INVITE_REG'] = array(
	'subject' => "[#user_name#] 당신을 초대 합니다 [#site_name#]",
	'message' => "[#user_name#] 당신을 초대 합니다 [#site_name#]",
);

$config['VALID_EMAIL'] = array(
	'subject' => "[#site_name#] 이메일 인정",
	'message' => "이 것은 당신이 [#site_name#] 에 종요한 이메일입니다, 그의 기능은 [#site_name#] 이메일로 계정을 인정하는 것입니다, 밑에 있는 링크를 클릭해서 인정을 완성하세요."
);

$config['FIND_PASSWORD'] = array(
	'subject' => "[#site_name#] 비밀번호 찾기",
	'message' => "당신은  [#site_name#] 에 비밀번호 찾기를 신청했습니다。<br />당신이 비밀번호 신청을 하지 않으면,이 메일을 무시하세요",
);

$config['QUESTION_INVITE'] = array(
	'subject' => "[#user_name#] 이/가 [#site_name#]에 질문 대답 초대를 했습니다: [#question_title#]",
	'message' => "[#user_name#] 이/가 [#site_name#]에 질문 대답 초대를 했습니다: [#question_title#]",
);

$config['FOLLOW_ME'] = array(
	'subject' => "[#user_name#] 이/가 [#site_name#]에 당신을 팔로우 했습니다.",
	'message' => "[#user_name#] 이/가 [#site_name#]에 당신을 팔로우 했습니다.",
	'link_title' => " [#user_name#] 의 개인 홈페이지를 봅니다.",
);

$config['NEW_ANSWER'] = array(
	'subject' => "당신이 [#site_name#] 에 팔로우한 질문에서 새 댓글을 남겼습니다: [#question_title#]",
	'message' => "당신이 [#site_name#] 에 팔로우한 질문에서 새 댓글을 남겼습니다: [#question_title#]",
);

$config['NEW_MESSAGE'] = array(
	'subject' => "[#user_name#] 이/가 [#site_name#]에 당신께서 쪽지를 보냈습니다",
	'message' => "[#user_name#] 이/가 [#site_name#]에 당신께서 쪽지를 보냈습니다. <br /><br />----- 쪽지 내용 -----<br /><br />[#message#]<br /><br />",
);

$config['INVITE_QUESTION'] = array(
	'subject' => "[#user_name#] 이/가 [#site_name#]에 당신이 질문 참석을 초대했습니다. [#question_title#]",
	'message' => "[#user_name#] 이/가 [#site_name#]에 당신이 질문 참석을 초대했습니다. [#question_title#]",
	'link_title' => "[#question_title#]",
);

$config['QUESTION_SHARE'] = array(
	'subject' => "[#user_name#] 이/가 [#site_name#]에 당신께서질문을 공유했습니다: [#question_title#]",
	'message' => "[#user_name#] 이/가 [#site_name#]에 당신께서질문을 공유했습니다: [#question_title#]",
);

$config['ANSWER_SHARE'] = array(
	'subject' => "[#user_name#] 이/가 [#site_name#]에 당신께서질문을 공유했습니다: [#question_title#]",
	'message' => "[#user_name#] 이/가 [#site_name#]에 당신께서질문을 공유했습니다: [#question_title#] [#answer_user#]：[#answer_content#]",
);

$config['TOPIC_SHARE'] = array(
	'subject' => "[#user_name#] 이/가[#site_name#]에 당신께서 화제를 공유 했습니다.: [#topic_title#]",
	'message' => "[#user_name#] 이/가[#site_name#]에 당신께서 화제를 공유 했습니다.: [#topic_title#]",
);

$config['QUESTION_MOD'] = array(
	'subject' => "[#user_name#] 이/가 [#site_name#] 당신이 발표한 질문을 수정 했습니다: [#question_title#]",
	'message' => "[#user_name#] 이/가 [#site_name#] 당신이 발표한 질문을 수정 했습니다: [#question_title#]",
);

$config['QUESTION_DEL'] = array(
	'subject' => "당신이 발표한 질문은 [#question_title#]관리원이 이미 삭제했습니다",
	'message' => "당신이 발표한 질문은 [#question_title#]관리원이 이미 삭제했습니다<br /><br />----- 삭제한 내용 -----<br /><br />[#question_detail#]<br /><br />-----------------------------<br /><br />의문 있으시면,관리원을 연락 하세요",
);

$config['REGISTER_DECLINE'] = array(
	'subject' => "당신이 [#site_name#]에 신청한 아이디가 신사를 통과 하지 않았다.",
	'message' => "당신이 [#site_name#]에 신청한 아이디가 신사를 통과 하지 않았다.<br /><br />----- 원인 -----<br /><br />[#message#]<br /><br />-----------------------------<br /><br />의문 있으시면,관리원을 연락 하세요",
);
