<?php

/*
 * 这个文件是 youpaiyunzhi 的一部分。
 *
 * (c) shsrain <shsrain@163.com>
 *
 * 对于全版权和许可信息，请查看分发此源代码的许可文件。
 */

 /**
  * 这是一个资源命令注册文件。
  *
  * @author shsrain <shsrain@163.com>
  */

// 咨询命令
$app['loader']->command('inquiry','InquiriesPublicCommand',$app);
$app['loader']->command('inquiry','InquiriesResolvedCommand',$app);
$app['loader']->command('inquiry','InquiriesByMeCommand',$app);
$app['loader']->command('inquiry','InquiriesShowCommand',$app);
$app['loader']->command('inquiry','InquiriesUpdateCommand',$app);
$app['buslocator']->register('InquiriesPublicCommand', new InquiriesPublicCommandHandler());
$app['buslocator']->register('InquiriesResolvedCommand', new InquiriesResolvedCommandHandler());
$app['buslocator']->register('InquiriesByMeCommand', new InquiriesByMeCommandHandler());
$app['buslocator']->register('InquiriesShowCommand', new InquiriesShowCommandHandler());
$app['buslocator']->register('InquiriesUpdateCommand', new InquiriesUpdateCommandHandler());

// 提问命令
$app['loader']->command('inquiry','AdvisoriesShowCommand',$app);
$app['loader']->command('inquiry','AdvisoriesReplyCommand',$app);
$app['loader']->command('inquiry','AdvisoriesUpdateCommand',$app);
$app['buslocator']->register('AdvisoriesShowCommand', new AdvisoriesShowCommandHandler());
$app['buslocator']->register('AdvisoriesReplyCommand', new AdvisoriesReplyCommandHandler());
$app['buslocator']->register('AdvisoriesUpdateCommand', new AdvisoriesUpdateCommandHandler());

// 提醒命令
$app['loader']->command('inquiry','RemindMsgUnreadCommand',$app);
$app['loader']->command('inquiry','RemindMsgUnreadAllCommand',$app);
$app['loader']->command('inquiry','RemindMsgReadCommand',$app);
$app['loader']->command('inquiry','RemindMsgReadAllCommand',$app);
$app['buslocator']->register('RemindMsgUnreadCommand', new RemindMsgUnreadCommandHandler());
$app['buslocator']->register('RemindMsgUnreadAllCommand', new RemindMsgUnreadAllCommandHandler());
$app['buslocator']->register('RemindMsgReadCommand', new RemindMsgReadCommandHandler());
$app['buslocator']->register('RemindMsgReadAllCommand', new RemindMsgReadAllCommandHandler());

// 关注命令
$app['loader']->command('inquiry','FocusPublicCommand',$app);
$app['buslocator']->register('FocusPublicCommand', new FocusPublicCommandHandler());

// 用户命令
$app['loader']->command('inquiry','UsersShowCommand',$app);
$app['buslocator']->register('UsersShowCommand', new UsersShowCommandHandler());

// 新闻命令
$app['loader']->command('news','NewsPublicCommand',$app);
$app['loader']->command('news','NewsDetailCommand',$app);
$app['loader']->command('news','ReplyPublicCommand',$app);
$app['loader']->command('news','ReplyUpdateCommand',$app);
$app['buslocator']->register('NewsPublicCommand', new NewsPublicCommandHandler());
$app['buslocator']->register('NewsDetailCommand', new NewsDetailCommandHandler());
$app['buslocator']->register('ReplyPublicCommand', new ReplyPublicCommandHandler());
$app['buslocator']->register('ReplyUpdateCommand', new ReplyUpdateCommandHandler());
