<?php

$config['INVITE_REG'] = array(
	'subject' => "[#user_name#] invites you to join [#site_name#]",
	'message' => "[#user_name#] invites you to join [#site_name#]",
);

$config['VALID_EMAIL'] = array(
	'subject' => "[#site_name#] E-mail verification",
	'message' => "This is an important E-mail for you from [#site_name#] to verify your account E-mail on [#site_name#]. Click the link below to complete verification."
);

$config['FIND_PASSWORD'] = array(
	'subject' => "[#site_name#] retrieve password.",
	'message' => "Your have submitted an request for retrieving password on [#site_name#] . <br />If you did not submit a password retrieval request, ignore this E-mail.",
);

$config['QUESTION_INVITE'] = array(
	'subject' => "[#user_name#] invited you to reply to the question: [#question_title#] on [#site_name#]",
	'message' => "[#user_name#] invited you to reply to the question: [#question_title#] on [#site_name#]",
);

$config['FOLLOW_ME'] = array(
	'subject' => "[#user_name#] followed you on [#site_name#]",
	'message' => "[#user_name#] followed you on [#site_name#]",
	'link_title' => "View [#user_name#]'s profile",
);

$config['NEW_ANSWER'] = array(
	'subject' => "The question you followed on [#site_name#] received a new reply: [#question_title#]",
	'message' => "The question you followed on [#site_name#] received a new reply: [#question_title#]",
);

$config['NEW_MESSAGE'] = array(
	'subject' => "[#user_name#] sent you a message on  [#site_name#]",
	'message' => "[#user_name#] sent you a message on  [#site_name#]<br /><br />----- Message Content -----<br /><br />[#message#]<br /><br />",
);

$config['INVITE_QUESTION'] = array(
	'subject' => "[#user_name#] invited you to participate in question [#question_title#] on [#site_name#]",
	'message' => "[#user_name#] invited you to participate in question [#question_title#] on [#site_name#]",
	'link_title' => "[#question_title#]",
);

$config['QUESTION_SHARE'] = array(
	'subject' => "[#user_name#] shared a question [#question_title#] with you on [#site_name#]",
	'message' => "[#user_name#] shared a question [#question_title#] with you on [#site_name#]",
);

$config['ANSWER_SHARE'] = array(
	'subject' => "[#user_name#] shared a topic [#topic_title#] with you on [#site_name#]",
	'message' => "[#user_name#] shared a topic [#topic_title#] with you on [#site_name#] [#answer_user#]：[#answer_content#]",
);

$config['TOPIC_SHARE'] = array(
	'subject' => "[#user_name#] shared a topic [#topic_title#] with you on [#site_name#]",
	'message' => "[#user_name#] shared a topic [#topic_title#] with you on [#site_name#]",
);

$config['QUESTION_MOD'] = array(
	'subject' => "[#user_name#] edited your question [#question_title#] on [#site_name#]",
	'message' => "[#user_name#] edited your question [#question_title#] on [#site_name#]",
);

$config['QUESTION_DEL'] = array(
	'subject' => "The question you posted [#question_title#] has been deleted by the administrator",
	'message' => "The question you posted [#question_title#] has been deleted by the administrator<br /><br />----- Deleted Question -----<br /><br />[#question_detail#]<br /><br />-----------------------------<br /><br />If there is any question, please contact the administrator.",
);

$config['REGISTER_DECLINE'] = array(
	'subject' => "Your registration request on [#site_name#] was not approved",
	'message' => "Your registration request on [#site_name#] was not approved<br /><br />----- Reason -----<br /><br />[#message#]<br /><br />-----------------------------<br /><br />If there is any question, please contact the administrator.",
);
