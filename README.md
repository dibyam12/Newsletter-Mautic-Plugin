# Process to add custom fields in Mautic Backend
1.	Go to Mautic backend UI by entering Mautic url.
2.	Click on gear icon of top right corner.
3.	Click on Custom Fields menu.
4.	On top right corner click on New button to create custom field in side Custom Field page.
5.	Create Category Custom Field as shown in screen shoot.  
  •	Note: Iif user want new category in future they need to add in this Option list

6.	Create Interval Custom Field as shown in screen shoot.  
7.	Create last News Letter Sent Date Custom Field as shown in screen shoot.  
  •	Note: This field is not required while user create and need not to show is form as well.
8.	After This Quick Add Contact Form is look like as shown in screen shoot. 
9.	Setting up backend is completed.
  •	Note: All columns are assigned as nf prefix to know that it is added for RssNewsletterBundle Plugin

# Process to install plugin in Mautic.
1.	Copy RssNewsletterBundle in Mautic backend code.
2.	Run command php bin/console cache:clear
3.	Run command php bin/console  mautic:plugins:reload
  •	After this Mautic plugin will install in Mautic server. Verify either plugin is installed or not.
  •	Goto Mautic backend
  •	Click on gear icon on top right cornor of UI.
  •	Click on Plugins menu. After this Plugins page will load.
  •	Check newly install plugin is there or not.

# WorkFlow of RssNewsletterBundle Plugin
  # 1. Command Execution: newsletter:process
    The command newsletter:process processes newsletters and sends emails to eligible users.
    Usage:
      ddev exec bin/console newsletter:process --limit=1000
    Options:
      •	--limit: The maximum number of users to process per batch. Default is 1000.
    Process Flow:
    1.	Fetch RSS Feed: The system fetches events from the configured RSS feed URL.
      o	RssHelper->fetchFeed($rssUrl)
    2.	Get Eligible Users: Fetch users who are eligible for newsletters based on their categories and last sent date.
      o	UserHelper->getEligibleUsers($limit)
    3.	Match Events to Categories: The user's categories are matched to the RSS events.
      o	User's categories are compared with the event categories to determine if they should receive an email.
    4.	Generate Email Content: For users with matching events, generate personalized email content.
      o	NewsletterService->generateEmailContent($user, $matchedEvents)
    5.	Send Email: The email is sent to eligible users with the generated content.
      o	EmailHelper->sendCustomEmail($user['email'], 'Your Personalized Events', $content)
    6.	Update User's Last Sent Date: After sending the email, the nf_last_sent_date for each user is updated.
      o	UserHelper->updateLastSentDate($userId)
  
   # 2. API Endpoint: /send
    The /send endpoint allows triggering the newsletter process programmatically via an HTTP request.
    URL:
      {yourURL}/api/send
    Request Method:
      GET

