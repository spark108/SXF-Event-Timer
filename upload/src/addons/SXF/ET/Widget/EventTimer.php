<?php

namespace SXF\ET\Widget;

use \XF\Widget\AbstractWidget;

class EventTimer extends AbstractWidget
{
	public function render()
	{
		$opt = $this->options;
		
		if (!$this->canViewSelectedUsers() || 
			!$this->canNotViewSelectedUsers() || 
			!$this->canViewSelectedUserGroups() || 
			!$this->canNotViewSelectedUserGroups())
		{
			return;
		}
	
		$viewParams = $this->options;
		
		$options = $this->getDefaultTemplateParams('options');
		
		$startDate = $viewParams['start'];
		$startDate = "{$startDate['year']}-{$startDate['month']}-{$startDate['day']} {$startDate['hour']}:{$startDate['minute']}";

		$secondForStart = strtotime($startDate);
		
		if ($secondForStart > time())
		{
			return;
		}
		
		$endDate = $viewParams['end'];
		$endDate = "{$endDate['year']}-{$endDate['month']}-{$endDate['day']} {$endDate['hour']}:{$endDate['minute']}";
		
		$secondForEnd = strtotime($endDate) - time();
		
		if ($opt['hide'] && $secondForEnd < 0 && ($secondForEnd + $opt['hide_time']) < 0)
		{
			return;
		}
		
		$viewParams['end'] = $endDate;
		$viewParams['start'] = $startDate;
		$viewParams['key'] = $options['widget']['key'];
		
		return $this->renderer('widget_sxf_et_eventTimer', $viewParams);
	}
	
	public function renderOptions()
	{
		$templateName = $this->getOptionsTemplate();
		if (!$templateName)
		{
			return '';
		}
		
		$userGroups = \XF::finder('XF:UserGroup')->fetch();
		
		$viewParams = $this->getDefaultTemplateParams('options');
		$viewParams += [
			'user_groups' => $userGroups
		];
		
		return $this->app->templater()->renderTemplate($templateName, $viewParams);
	}
	
	public function canViewSelectedUsers()
	{
		$opt = $this->options;
		
		if ($opt['users'])
		{
			$visitor = \XF::visitor();
			$users = explode(',', $opt['users']);
			foreach ($users as $username)
			{
				if ($visitor->username == trim($username))
				{
					return true;
				}
			}
			
			return false;
		}
		
		return true;
	}
	
	public function canNotViewSelectedUsers()
	{
		$opt = $this->options;
		
		if ($opt['not_users'])
		{
			$visitor = \XF::visitor();
			$users = explode(',', $opt['not_users']);
			foreach ($users as $username)
			{
				if ($visitor->username == trim($username))
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	public function canViewSelectedUserGroups()
	{
		$opt = $this->options;
		
		if ($opt['user_groups'] && $opt['user_groups'][0])
		{
			$visitor = \XF::visitor();
			foreach ($opt['user_groups'] as $user_group_id)
			{
				if ($visitor->user_group_id == $user_group_id)
				{
					return true;
				}
			}
			
			return false;
		}
		
		return true;
	}
	
	public function canNotViewSelectedUserGroups()
	{
		$opt = $this->options;
		
		if ($opt['not_user_groups'])
		{
			$visitor = \XF::visitor();
			foreach ($opt['not_user_groups'] as $user_group_id)
			{
				if ($visitor->user_group_id == $user_group_id)
				{
					return false;
				}
			}
		}
		
		return true;
	}
}