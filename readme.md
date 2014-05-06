EDM Vertical

## Notes
- viewHeroAlignment and viewHeroHeight removed, should be added by themes as needed.

## Post Types
The standard Post and Page post types are available as-is.

### Event (Event)

* Title                   (summary)
* Description             (description)
* Image(s)
* Start Date              (startDate)
* End Date                (endDate)
* Start Time              (doorTime)
* End Time                (endDate)
* Duration
* Ticket Purchase URL
* RSVP URL                (Facebook only, on DDP)
* PublishStatus           (post_status - Publish, Draft, Visible, Future)
* Visibility              (post_visibility) ??
* Status                  (eventStatus - Cancelled, Postponed, Rescheduled, Scheduled, Past)
* @ City
* @ State                 (custom categorical term for user interaction)
* @ Age Restriction
* @ Event Type
* @ Event Tour
* Location                (Valid physical address)
* _location.latitude
* _location.longitude
* -> Artist(s)
* -> Promoter(s)
* -> Venue(s)
* -> Child Event(s)
* -> Parent Event

### Event Update (Future)

### Artist (Performers)
Excerpt, for now, is not used and hidden.

* Name                    (post_title)
* Description             (post_content)
* Social URL(s)
* Official URL
* Image: Headshot
* Image: Portrait
* Image: Landscape
* Image: Logo
* Image: Additional(s)

### Performance (PerformanceEvent)
An artist, or artists, performing at an Event.

* Stage                   (stage an artist is performing at)
* Featured                (artist featured at event)
* Start Date
* Start Time
* End Date
* End Time

### Photo Gallery
May be child of an Event, or used independently.

### Video Gallery
May be child of an Event, or used independently.

### Offer (Future)
An offer for an event.

### Venue (Place)
A physical location at which an event may take place.

* Name
* Description
* Address
* _location.latitude
* _location.longitude
* Official URL
* Social URL(s)
* Image: Logo
* Image: Additional Image(s)
* @ Venue Type
* @ City
* @ State
* @ Country

### Promoter (Organization)
Similar to artist.

* Name
* Description
* Official URL
* Social URL(s)
* Image: Logo

## Post Formats

* Gallery   - A gallery of images. Post will likely contain a gallery shortcode and will have image attachments.
* Video     - A single video. The first <video /> tag or object/embed in the post content could be considered the video. Alternatively, if the post consists only of a URL, that will be the video URL. May also contain the video as an attachment to the post, if video support is enabled on the blog (like via a plugin).
* Audio     - An audio file. Could be used for Podcasting.
* Status    - A short status update, similar to a Twitter status update.

## Taxonomies

### Category
Categorical, unrestricted. Available to all "content" post types.

### Tag
Categorical, unrestricted. Available to all "content" post types.

### Event Type (eventType)
Categorical, unrestricted. To include terms such as "Festival"

### Venue Type (venueType)
Categorical, unrestricted.

### Event Tour (eventTour)
Categorical, unrestricted.

### City, State and Country
Categorical, auto-generated. Standard location-based terms which may be hidden from control panel as they are generate automatically based on a physical address.

### Event Age Restriction (EventAgeRestriction|EventAllowedAgeRange)
Categorical, unrestricted.

### Artist Genre (artistGenre)
Categorical, unrestricted.

## User Roles & Capabilities

* Media Provider - Providers of video and photo content.
  - Submit Image
  - Submit Video

## Resources
http://schema.org/NightClub