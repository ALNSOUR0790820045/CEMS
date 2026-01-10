# Notifications & Alerts Module Documentation

## Overview

The Notifications & Alerts Module provides a comprehensive real-time notification and alert system for the CEMS application. It includes support for multiple notification types, user preferences, automated alert rules, and scheduled notifications.

## Database Tables

### 1. `notifications` - Notifications Table
Stores all notifications for users.

**Fields:**
- `id` - Primary key
- `uuid` - Unique identifier for external references
- `type` - Notification type (info, success, warning, error, reminder)
- `category` - Category (system, approval, deadline, alert, message)
- `title` - Arabic title
- `title_en` - English title
- `body` - Arabic message body
- `body_en` - English message body
- `data` - JSON data payload
- `notifiable_type` - Polymorphic type (usually User)
- `notifiable_id` - User ID receiving the notification
- `read_at` - Timestamp when notification was read
- `clicked_at` - Timestamp when notification was clicked
- `action_url` - Optional URL for action button
- `icon` - Icon identifier
- `priority` - Priority level (low, normal, high, urgent)
- `expires_at` - Expiration timestamp
- `company_id` - Company reference
- `timestamps` - created_at, updated_at

### 2. `notification_preferences` - User Notification Preferences
Stores user preferences for notification channels and types.

**Fields:**
- `id` - Primary key
- `user_id` - User reference
- `notification_type` - Type of notification
- `channel_email` - Enable email notifications
- `channel_sms` - Enable SMS notifications
- `channel_push` - Enable push notifications
- `channel_in_app` - Enable in-app notifications
- `is_enabled` - Master enable/disable switch
- `quiet_hours_start` - Quiet hours start time
- `quiet_hours_end` - Quiet hours end time
- `timestamps` - created_at, updated_at

### 3. `alert_rules` - Automated Alert Rules
Defines rules for automatic notifications based on events.

**Fields:**
- `id` - Primary key
- `name` - Arabic rule name
- `name_en` - English rule name
- `description` - Rule description
- `event_type` - Event trigger type
- `conditions` - JSON conditions for triggering
- `recipients_type` - Recipient type (user, role, department, all)
- `recipients_ids` - JSON array of recipient IDs
- `channels` - JSON array of notification channels
- `message_template` - Custom message template
- `is_active` - Active status
- `company_id` - Company reference
- `timestamps` - created_at, updated_at

### 4. `notification_logs` - Notification Delivery Logs
Tracks notification delivery status across channels.

**Fields:**
- `id` - Primary key
- `notification_id` - Notification reference
- `channel` - Delivery channel (email, sms, push, in_app)
- `recipient_id` - User reference
- `recipient_email` - Email address used
- `recipient_phone` - Phone number used
- `status` - Delivery status (pending, sent, delivered, failed, bounced)
- `sent_at` - Sent timestamp
- `delivered_at` - Delivered timestamp
- `failed_at` - Failed timestamp
- `error_message` - Error details if failed
- `retry_count` - Number of retry attempts
- `timestamps` - created_at, updated_at

### 5. `scheduled_notifications` - Scheduled Notifications
Notifications scheduled for future delivery.

**Fields:**
- `id` - Primary key
- `title` - Notification title
- `body` - Notification body
- `scheduled_at` - Scheduled delivery time
- `repeat_type` - Repeat frequency (once, daily, weekly, monthly)
- `recipients_type` - Recipient type (user, role, department, all)
- `recipients_ids` - JSON array of recipient IDs
- `status` - Status (pending, sent, cancelled)
- `created_by_id` - Creator user reference
- `company_id` - Company reference
- `timestamps` - created_at, updated_at

## API Endpoints

### Notifications

#### List Notifications
```http
GET /api/notifications
```
Query Parameters:
- `type` - Filter by type
- `category` - Filter by category
- `priority` - Filter by priority
- `read` - Filter by read status (0 or 1)
- `per_page` - Pagination size (default: 15)

#### Get Unread Notifications
```http
GET /api/notifications/unread
```

#### Get Unread Count
```http
GET /api/notifications/unread-count
```

#### Mark as Read
```http
POST /api/notifications/{id}/read
```

#### Mark All as Read
```http
POST /api/notifications/read-all
```

#### Delete Notification
```http
DELETE /api/notifications/{id}
```

#### Clear All Notifications
```http
DELETE /api/notifications/clear-all
```

#### Send Notification (Admin)
```http
POST /api/notifications/send
```
Body:
```json
{
  "title": "Notification Title",
  "body": "Notification message",
  "type": "info",
  "category": "system",
  "priority": "normal",
  "user_ids": [1, 2, 3],
  "action_url": "https://example.com/action",
  "expires_at": "2026-02-01T12:00:00Z"
}
```

#### Broadcast Notification (Admin)
```http
POST /api/notifications/broadcast
```
Sends to all users in company.
Body:
```json
{
  "title": "Broadcast Message",
  "body": "This is a broadcast message",
  "type": "info",
  "priority": "normal"
}
```

### Notification Preferences

#### List Preferences
```http
GET /api/notification-preferences
```

#### Update Preferences (Bulk)
```http
PUT /api/notification-preferences
```
Body:
```json
{
  "preferences": [
    {
      "notification_type": "budget_exceeded",
      "channel_email": true,
      "channel_sms": false,
      "channel_push": true,
      "channel_in_app": true,
      "is_enabled": true,
      "quiet_hours_start": "22:00",
      "quiet_hours_end": "08:00"
    }
  ]
}
```

#### Update Single Preference
```http
PUT /api/notification-preferences/{type}
```
Body:
```json
{
  "channel_email": true,
  "channel_sms": true,
  "is_enabled": true
}
```

### Alert Rules

#### List Alert Rules
```http
GET /api/alert-rules
```
Query Parameters:
- `event_type` - Filter by event type
- `is_active` - Filter by status
- `search` - Search in name/description
- `per_page` - Pagination size

#### Create Alert Rule
```http
POST /api/alert-rules
```
Body:
```json
{
  "name": "تجاوز الميزانية",
  "name_en": "Budget Exceeded",
  "description": "Alert when budget is exceeded",
  "event_type": "budget_exceeded",
  "conditions": {"threshold": 100},
  "recipients_type": "role",
  "recipients_ids": [1, 2],
  "channels": ["email", "in_app"],
  "message_template": "Budget exceeded: {amount}",
  "is_active": true
}
```

#### Show Alert Rule
```http
GET /api/alert-rules/{id}
```

#### Update Alert Rule
```http
PUT /api/alert-rules/{id}
```

#### Delete Alert Rule
```http
DELETE /api/alert-rules/{id}
```

#### Toggle Alert Rule
```http
POST /api/alert-rules/{id}/toggle
```

#### Test Alert Rule
```http
POST /api/alert-rules/{id}/test
```
Body:
```json
{
  "test_data": {"amount": 150000}
}
```

### Scheduled Notifications

#### List Scheduled Notifications
```http
GET /api/scheduled-notifications
```
Query Parameters:
- `status` - Filter by status
- `from_date` - Filter by date range start
- `to_date` - Filter by date range end
- `per_page` - Pagination size

#### Create Scheduled Notification
```http
POST /api/scheduled-notifications
```
Body:
```json
{
  "title": "Scheduled Reminder",
  "body": "This is a scheduled reminder",
  "scheduled_at": "2026-02-01T09:00:00Z",
  "repeat_type": "once",
  "recipients_type": "user",
  "recipients_ids": [1, 2, 3]
}
```

#### Show Scheduled Notification
```http
GET /api/scheduled-notifications/{id}
```

#### Update Scheduled Notification
```http
PUT /api/scheduled-notifications/{id}
```

#### Delete Scheduled Notification
```http
DELETE /api/scheduled-notifications/{id}
```

#### Cancel Scheduled Notification
```http
POST /api/scheduled-notifications/{id}/cancel
```

## Events & Listeners

### Events

The following events trigger automatic notifications:

1. **BudgetExceededEvent** - Triggered when budget is exceeded
2. **DeadlineApproachingEvent** - Triggered when deadline is approaching
3. **ApprovalRequestedEvent** - Triggered when approval is needed
4. **ContractExpiringEvent** - Triggered when contract is expiring
5. **LowStockEvent** - Triggered when stock is low
6. **PaymentOverdueEvent** - Triggered when payment is overdue

### Listeners

1. **SendNotificationListener** - Processes events and creates notifications based on alert rules
2. **LogNotificationListener** - Logs notification delivery attempts

### Usage Example

```php
use App\Events\Notifications\BudgetExceededEvent;

// Trigger an event
event(new BudgetExceededEvent($project, $budget, $spent, $company));

// The SendNotificationListener will automatically:
// 1. Find active alert rules for 'budget_exceeded' event
// 2. Get recipients based on rules
// 3. Create notifications for each recipient
// 4. Log delivery attempts
```

## Models

### Notification Model

**Key Methods:**
- `markAsRead()` - Mark notification as read
- `markAsClicked()` - Mark notification as clicked
- `isRead()` - Check if notification is read
- `isExpired()` - Check if notification is expired

**Scopes:**
- `unread()` - Get unread notifications
- `read()` - Get read notifications
- `byType($type)` - Filter by type
- `byCategory($category)` - Filter by category
- `byPriority($priority)` - Filter by priority
- `notExpired()` - Get non-expired notifications

### NotificationPreference Model

**Key Methods:**
- `isChannelEnabled($channel)` - Check if channel is enabled
- `isInQuietHours()` - Check if current time is in quiet hours

### AlertRule Model

**Key Methods:**
- `toggle()` - Toggle active status
- `matchesConditions($data)` - Check if conditions match
- `getRecipients()` - Get recipients based on type

**Scopes:**
- `active()` - Get active rules
- `byEventType($type)` - Filter by event type

### ScheduledNotification Model

**Key Methods:**
- `cancel()` - Cancel scheduled notification
- `markAsSent()` - Mark as sent
- `getRecipients()` - Get recipients based on type

**Scopes:**
- `pending()` - Get pending notifications
- `due()` - Get due notifications

## Testing

Test files are located in `tests/Feature/`:
- `NotificationTest.php` - Tests notification CRUD operations
- `NotificationPreferenceTest.php` - Tests preference management
- `AlertRuleTest.php` - Tests alert rule functionality
- `ScheduledNotificationTest.php` - Tests scheduled notifications

### Running Tests

```bash
php artisan test --filter NotificationTest
php artisan test --filter NotificationPreferenceTest
php artisan test --filter AlertRuleTest
php artisan test --filter ScheduledNotificationTest
```

**Note:** Due to pre-existing duplicate migration issues in the repository (multiple migrations for cities, currencies, and other tables), tests may fail during database setup. This is not related to the Notifications module implementation. The duplicate migrations exist in the repository before this module was added.

## Installation

1. Run migrations:
```bash
php artisan migrate
```

2. The module is now ready to use via the API endpoints.

## Future Enhancements

- Email notification service integration
- SMS notification service integration
- Push notification service integration
- WebSocket for real-time in-app notifications
- Notification templates management
- Advanced filtering and search
- Notification analytics dashboard
- Bulk operations API
- Notification archiving

## Support

For issues or questions, please contact the development team.
