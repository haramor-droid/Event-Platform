# Event Discovery and Management Platform
## Requirements Specification

### 1. Document Information
* **Project:** Event Discovery and Management Platform (Primary WIL Project)
* **Organisation:** Teco IT Solutions, Work-Integrated Learning (WIL) placement
* **Author:** PJ-Amor Harakuta (224063596)
* **Industry Supervisor:** Samuel Moyo
* **Version:** 0.1 (Draft)
* **Date:** 27 October 2026
* **Status:** Draft. Not yet reviewed or approved by the industry supervisor.

**Priority Legend:**
* `[M]` Must Have (Core feature, built first)
* `[S]` Should Have (Additional feature)
* `[C]` Could Have (Built only if time allows)
* `[Cond]` Conditional (Depends on external access, cost, or approval)

*(All requirements are currently marked as "Proposed" and will be updated to "Confirmed" after supervisor review).*

---

### 2. Project Overview
The Event Discovery and Management Platform is a web application that puts events in one place. People can find out what is happening and save the events that interest them. Organisers can publish and manage their own listings, and an administrator makes sure what appears on the platform is relevant, lawful and current.

It supports two kinds of listing: **One-off events** (like a concert or a charity fundraiser) and **Recurring events** (like a weekly adult skating night or a monthly workshop). Permanent businesses and venues (roller rinks, cinemas, sports clubs) can register as organisers and publish their schedules in the same way as a promoter.

The platform is a curated directory, not a social feed. Current and upcoming events are front and centre. Once an event has passed it is archived, so it leaves the discovery pages but its record is kept. The system will be built using free tools (HTML, CSS, JavaScript, Bootstrap 5, PHP, MySQL, Git).

---

### 3. Problem Statement
Information about events is scattered across social media pages, messaging groups, and individual event pages, causing users to miss out. Organisers face the opposite problem: their listings compete with everything else on those feeds, and a venue with a regular weekly session has to keep reposting it to stay visible.

The platform solves this with one central place to discover events and one simple way for organisers to publish them. It keeps old listings out of the way and keeps what remains relevant.

---

### 4. Target Users

**4.1 Attendees (End Users)**
People looking for things to do, see, or learn.
* **Socialites and partygoers:** Nightlife, concerts, festivals, and parties.
* **Professionals and learners:** Seminars, workshops, and networking events.
* **Families and casual groups:** Leisure activities such as pottery or archery.
* **Sports and hobby enthusiasts:** Tournaments, club meetups, and regular sessions (e.g., skating nights).

**4.2 Organisers**
The people and businesses who provide the events.
* **Permanent venues and businesses:** Roller rinks, bowling alleys, cinemas, and sports clubs with recurring activities.
* **Professional event promoters:** Companies running concerts, fundraisers, and festivals.
* **Independent creators:** Workshop hosts, educators, and community leaders.
*(Note: Anyone can register as an organiser. Organiser accounts can also use attendee features).*

**4.3 Administrators**
The people who look after the content. They review submitted listings, remove invalid ones, and keep the platform relevant. Administrator accounts are created by the system owner and cannot be registered publicly.

---

### 5. User Needs

**Attendees need to:**
* Find events that are happening soon without searching several platforms.
* Narrow results by category, date, and location.
* See everything that matters about an event on one page (location, tickets, etc.).
* Keep track of events they care about and be told if plans change or cancel.

**Organisers need to:**
* Publish an event quickly with a poster or photo and key details.
* Know where their listing stands (and why, if rejected).
* Correct logistics (venue/time) without starting the approval process over.
* Set up a regular session once instead of reposting it every week.
* See whether people are showing interest (engagement).

**Administrators need to:**
* See what is waiting for review in one place.
* Reject or remove unsuitable events with a reason the organiser can read.
* Trust that old events leave the main feed automatically.

---

### 6. Functional Requirements

#### 6.1 Business Rules and Content Lifecycle
| ID    |                                           Rule                                                                     |
|-------|-------------------------------------------------------------------------------------------------------------------|
| BR-01 | **Content relevance.** The platform prioritises current and upcoming events. |
| BR-02 | **Archive, never delete.** Passed events move to Archived. Data is kept for stats, but hidden from discovery. |
| BR-03 | **Active listings.** Organisers may keep multiple active listings simultaneously, provided they are current. |
| BR-04 | **Approval before publishing.** Every new listing is reviewed by an admin before going live. |
| BR-05 | **Acceptable content.** Real, lawful events only. Adult/explicit events are not accepted. |
| BR-06 | **Locked/Editable fields.** Once published, core details (poster, title, category, description) are locked. Logistics (venue, time, links) remain editable. |
| BR-07 | **Re-approval on change.** Changing a locked field returns the listing to Pending Approval. |
| BR-08 | **Logged edits.** Every editable change is logged, and users who saved the event are notified. |
| BR-09 | **Cancelled events.** A cancelled event stays in the discovery feed, labelled Cancelled, for 24 hours after the cancellation, or until its event date if that comes sooner. After that it leaves the feed. The event page keeps working by direct link, with a clear Cancelled banner, until the original event date has passed, then the listing is archived. A cancelled event stays in the saved list of anyone who saved it, labelled Cancelled, until they remove it or the date passes. Users who saved the event or added it to their calendar are notified. The 24 hour period is a setting, not a fixed value. |
| BR-10 | **Recurring events.** Approved once as a series. Occurrences move through the lifecycle individually. |
| BR-11 | **Keeping series current.** Prompt organisers every 3 months to confirm a series is still running. |
| BR-12 | **Open registration.** Anyone can register as an attendee or organiser. |
| BR-13 | **Time zone.** All times use Africa/Windhoek time (CAT, UTC+2). |
| BR-14 | **Undoing a cancellation.** An organiser can reverse a cancellation. The listing returns to Published without new approval, because no locked field has changed, and users who saved it are notified that it is back on. |
| BR-15 | **Cancelled or postponed.** Cancelled means the event is not happening at all. If an event only moves to a new date, time or place, the organiser edits those fields as a normal change and users are notified. Cancelling one occurrence of a series does not cancel the series (BR-10). |

**Lifecycle States:** `Draft -> Pending Approval -> Published -> Archived` (with branches for Rejected, Removed, and Cancelled).

#### 6.2 Attendee Requirements
| ID    | Requirement                                                                                              |Priority |
|-------|----------------------------------------------------------------------------------------------------------|---------|
| UR-01 | Register, log in, and manage profile.                                                                    | [M] |
| UR-02 | Select interests to highlight matching events in discovery.                                              | [M] |
| UR-03 | Browse current and upcoming events.                                                                      | [M] |
| UR-04 | Search for events and filter by category, date, and location.                                            | [M] |
| UR-05 | Open an event page showing poster, title, category, description, date, time, venue, and organiser.       | [M] |
| UR-06 | Save/unsave events and view a list of saved events.                                                      | [M] |
| UR-07 | Follow an external ticket link provided by the organiser.                                                | [S] |
| UR-08 | See an event's location on a map.                                                                        | [S] |
| UR-09 | Add an event to a personal calendar and view it.                                                         | [S] |
| UR-10 | Set in-platform reminders for events they choose.                                                        | [S] |
| UR-11 | The attendee shall receive an in-platform notification when an event they saved or added to their calendar is cancelled, is back on after a cancellation, or has its location, date or time changed. A cancelled event stays in their saved list, labelled Cancelled.           | [S] | Proposed |
| UR-12 | See the upcoming sessions of a recurring event.                                                          | [S] |
| UR-13 | See the organiser's social media links.                                                                  | [C] |
| UR-14 | See suggestions of similar events on an event page.                                                      | [C] |
| UR-15 | Reset a forgotten password.                                                                              | [Cond] |
| UR-16 | Receive notifications outside the platform (SMS or email).                                               | [Cond] |

#### 6.3 Organiser Requirements
| ID    | Requirement                                                                                                   | Priority |
|-------|---------------------------------------------------------------------------------------------------------------|----------|
| OR-01 | Register, log in, and manage an organisation profile.                                                         | [M] |
| OR-02 | Create an event listing with title, category, description, date, time, location, and a required poster image. | [M] |
| OR-03 | Save a listing as a draft and submit for approval.                                                            | [M] |
| OR-04 | See the status of listings, and rejection reasons.                                                            | [M] |
| OR-05 | View and manage active and archived listings.                                                                 | [M] |
| OR-06 | Edit editable fields of a published listing (takes effect immediately).                                       | [M] |
| OR-07 | Change a locked field (triggers re-approval).                                                                 | [M] |
| OR-08 | Provide an external ticket link.                                                                              | [S] |
| OR-09 | Cancel a published event.                                                                                     | [S] |
| OR-10 | Add further images to a listing.                                                                              | [S] |
| OR-11 | Create a recurring listing that repeats weekly or monthly.                                                    | [S] |
| OR-12 | Manage a series (cancel one occurrence, change schedule, end series).                                         | [S] |
| OR-13 | Respond to prompts confirming a series is still running.                                                      | [S] |
| OR-14 | Add social media links to their profile or listings.                                                          | [C] |
| OR-15 | Add a short promotional video.                                                                                | [C] |
| OR-16 | View basic engagement statistics (views, saves, calendar adds).                                               | [C] |
| OR-17 | Receive a daily summary of engagement.                                                                        | [C] |
| OR-18 | Duplicate an archived listing as a new draft.                                                                 | [C] |
| OR-19 | Pay a publishing fee per post before a listing goes live.                                                   | [Cond] |
| OR-20 | The organiser shall be able to undo the cancellation of an event (BR-14).                                      | [S] | Proposed |

#### 6.4 Administrator Requirements
| ID    |                                        Requirement                            | Priority |
|-------|-------------------------------------------------------------------------------|----------|
| AR-01 | Log in securely (no public registration for admins).                          | [M] |
| AR-02 | View submitted listings in an approval queue.                                 | [M] |
| AR-03 | Approve or reject a listing (with a reason).                                  | [M] |
| AR-04 | Remove a published listing (with a reason) and restore it later.              | [M] |
| AR-05 | Archive outdated or invalid listings manually.                                | [M] |
| AR-06 | Manage the list of event categories.                                          | [S] |
| AR-07 | Suspend and reactivate user/organiser accounts.                               | [S] |
| AR-08 | View a monitoring dashboard of recent activity (new subs, edits, removals).   | [S] |
| AR-09 | Mark organisers as "trusted" to bypass approval (but still allow removal).    | [C] |

#### 6.5 System Behaviour
| ID     |                                   Requirement                                           | Priority |
|--------|-----------------------------------------------------------------------------------------|----------|
| SYS-01 | Automatically archive an event once its end date/time passes.                           | [M] |
| SYS-02 | Compare edited locked fields against approved versions to trigger re-approval.          | [M] |
| SYS-03 | Log every change to an editable field (time, user, old/new values).                     | [M] |
| SYS-04 | Create dated occurrences of recurring series 8 weeks in advance.                        | [S] |
| SYS-05 | Prompt organisers every 3 months to confirm a series is running; archive if no response.| [S] |
| SYS-06 | Deliver in-platform notifications for cancellations and changes.                        | [S] |
| SYS-07 | The system shall remove a cancelled event from the discovery feed 24 hours after the cancellation (or at the event date if sooner), keep its page available by direct link until the event date has passed, and then archive it. The 24 hour period shall be configurable.  | [S] | Proposed |
---

### 7. Non-Functional Requirements
| ID     |          Area   |                              Requirement                                     |
|--------|-----------------|------------------------------------------------------------------------------|
| NFR-01 | Usability       | Responsive layout for mobile (down to 360px) and desktop.                    |
| NFR-02 | Performance     | Discovery and event pages should load in ~3 seconds on mobile.               |
| NFR-03 | Media           | Poster uploads accept JPG, PNG, WebP up to 5MB. Images compressed on upload. |
| NFR-04 | Security        | Passwords hashed; PDO prepared statements; input validation/output escaping. |
| NFR-05 | Access Control  | Server-side checks for role-based permissions (hiding buttons isn't enough). |
| NFR-06 | Uploads         | Check file types/sizes; store files securely so they cannot execute as code. |
| NFR-07 | Privacy         | Collect only necessary data; keep credentials out of Git.                    |
| NFR-08 | Data Retention  | Physical deletion is prohibited; use the archive system.                     |
| NFR-09 | Accessibility   | Forms have labels; good contrast; keyboard navigable; alt text for posters.  |
| NFR-10 | Compatibility   | Works on current Chrome, Firefox, Edge, and mobile browsers.                 |
| NFR-11 | Maintainability | Code in version control; database schema and setup documented.               |
| NFR-12 | Cost            | Core system runs on free tools. No paid service required.                    |
| NFR-13 | Time Handling   | Dates/times stored consistently in Africa/Windhoek time (CAT, UTC+2).        |

---

### 8. User Stories
| ID    |                                                     Story                                                      | Linked Req   |
|-------|----------------------------------------------------------------------------------------------------------------|--------------|
| US-01 | As an attendee, I want to register and choose my interests, so that the platform shows me events I care about. | UR-01, UR-02 |
| US-02 | As an attendee, I want to browse what is on soon, so that I can find something to do.                          | UR-03 |
| US-03 | As an attendee, I want to filter by category, date, and location, so I can find events near me.                | UR-04 |
| US-04 | As an attendee, I want to see all details on one page, so I know what the event is.                            | UR-05 |
| US-05 | As an attendee, I want to save events, so I can come back to them.                                             | UR-06 |
| US-06 | As an attendee, I want to open the ticket link, so I can buy a ticket.                                         | UR-07 |
| US-07 | As an attendee, I want to see the venue on a map, so I can get there.                                          | UR-08 |
| US-08 | As an attendee, I want to add an event to my calendar and set a reminder.                                      | UR-09, UR-10 |
| US-09 | As an attendee, I want to be told if a saved event is cancelled or moved.                                      | UR-11, SYS-06 |
| US-10 | As an attendee, I want to see upcoming dates of a weekly session, so I can plan.                               | UR-12 |

| US-11 | As an organiser, I want to register as a business/venue, so I can publish events.                              | OR-01 |
| US-12 | As an organiser, I want to create a listing with a poster and submit it.                                       | OR-02, OR-03 |
| US-13 | As an organiser, I want to see listing status/rejection reasons so I know what to fix.                         | OR-04 |
| US-14 | As an organiser, I want to correct venue/time after publishing without re-approval.                            | OR-06, SYS-03 |
| US-15 | As an organiser, I want to cancel an event so attendees are warned.                                            | OR-09 |
| US-16 | As a venue owner, I want to set up a weekly session once, so I don't repost weekly.                            | OR-11, SYS-04 |
| US-17 | As a venue owner, I want to cancel one session or end the series.                                              | OR-12, OR-13 |
| US-18 | As an organiser, I want a daily summary of views/saves to track interest.                                      | OR-16, OR-17 |

| US-19 | As an admin, I want a queue of pending listings to review quickly.                                             | AR-02, AR-03 |
| US-20 | As an admin, I want to remove rule-breaking listings and tell the organiser why.                               | AR-04 |
| US-21 | As an admin, I want to see recent activity in one place for monitoring.                                        | AR-08 |
| US-22 | As an admin, I want past events to leave the feed automatically.                                               | SYS-01 |

---

### 9. User Workflows

**WF-01: Finding and saving an event (Attendee)**
1. Register/login and pick interests.
2. Browse upcoming events (matching ones highlighted).
3. Search/filter by category, date, or location.
4. Open an event page to read details.
5. Save the event (optional: add to calendar and set reminder).
6. Open ticket link or map from the event page.
*(Alternative: If event is cancelled/moved, user receives a notification and the page updates).*

**WF-02: Publishing an event (Organiser & Admin)**
1. Organiser logs in and starts a new listing.
2. Enters details, uploads poster, saves draft or submits.
3. Listing moves to Pending Approval.
4. Admin reviews against acceptable content rules.
5. If approved: Published. If rejected: Organiser sees reason, corrects, and resubmits.

**WF-03: Changing a published listing (Organiser)**
1. Organiser edits a published listing.
2. System compares locked fields to approved version.
3. If only editable fields changed: Goes live immediately, logged, savers notified.
4. If locked field changed: Returns to Pending Approval and hidden until approved.

**WF-04: Running a recurring series (Organiser)**
1. Organiser creates listing and sets it to repeat.
2. Admin approves the series once.
3. System generates occurrences 8 weeks ahead.
4. Each occurrence archives individually after passing.
5. Organiser can cancel a session or end the series. System prompts every 3 months to confirm it's running.

 **WF-05: Cancelling an event (organiser)**

1. The organiser opens a published event and cancels it.
2. The listing stays in the discovery feed, labelled Cancelled, for 24 hours (or until the event date if that is sooner).
3. Users who saved it or added it to their calendar are notified. It stays in their saved list, labelled Cancelled.
4. After 24 hours the event leaves the feed. Its page still works by direct link and shows a Cancelled banner until the original event date has passed.
5. After the event date passes, the listing is archived.

Alternative path: if the organiser undoes the cancellation, the listing returns to Published without approval and users who saved it are told it is back on.

**WF-06: Monitoring and removal (Admin)**
1. Admin checks monitoring view for new submissions/edits.
2. If a listing breaks rules, Admin removes it with a reason.
3. Organiser corrects/resubmits, or Admin restores it.
4. Admin archives outdated listings manually.

---

### 10. Feature Priorities

**Must Have (Core):** UR-01 to UR-06, OR-01 to OR-07, AR-01 to AR-05, SYS-01 to SYS-03.
**Should Have (Additional):** UR-07 to UR-12, OR-08 to OR-13, OR-20, AR-06 to AR-08, SYS-04 to SYS-07.
**Could Have:** UR-13, UR-14, OR-14 to OR-18, AR-09.
**Conditional:** UR-15, UR-16, OR-19.

**Differences from Project Outline:**
* Poster image required on every listing (moves to core).
* Recurring events and venues as organisers added as Should Have.
* Cancelled events, locked field rules, and "still running" prompts added to maintain accuracy.
* Reviews are left out for now (too much moderation needed for MVP).

---

### 11. External Integrations
The external integrations are still being researched and will be completed in the next version of this document (see GitHub issues #2, #3 and #4). Until then, none of them is a firm requirement.


### 12. Scope and Limitations

**In Scope:**
* Registration/login for all roles.
* Event discovery (search, filters, saving).
* Listings with required poster, approval workflow, lifecycle.
* Organiser management (editing, cancelling, archiving).
* Admin approval, removal, monitoring.
* Recurring events and venues as organisers.
* In-platform reminders/notifications.

**Out of Scope (For Now):**
* Reviews/ratings.
* Selling tickets/processing payments inside the platform.
* Social feed/comments/messaging.
* Native mobile app (responsive web only).
* Multiple languages.
* Identity verification beyond admin approval.

---

### 13. Open Questions
| ID    |                                                            Question    | Current Stance |
|-------|------------------------------------------------------------------------|----------------|
| OQ-01 | Should the platform process ticket payments directly, or just link out? | External link is the requirement. Direct payment is future/conditional. |
| OQ-02 | Is a publishing fee per post a firm requirement? | Conditional. Needs payment gateway if required. |
| OQ-03 | Can a free SMS gateway be secured, or should notifications stay in-platform? | In-platform only for now. |
| OQ-04 | Can email be sent reliably for password resets? | Conditional until confirmed. |
| OQ-05 | What exactly does the acceptable content rule cover? | Lawful events only; admin decides edge cases. |
| OQ-06 | Is the list of locked fields correct? | Locked: poster, title, category, description. Editable: date, time, venue. May change when development starts |
| OQ-07 | If trusted organisers are built, how many clean listings earn trust? | Not decided. Only if AR-09 is built. |
| OQ-08 | Are 8 weeks of occurrences and 3-month prompts the right settings? | Starting values; can be changed. |
| OQ-09 | What are the starting event categories? | To be agreed during design. |
| OQ-10 | How are administrator accounts created? | Created by system owner. One at first. |
| OQ-11 | Should an organiser account also act as an attendee account? | Not sure, maybe as collaborations between organisations in the future |
| OQ-12 | Is 24 hours the right time for a cancelled event to stay in the discovery feed? | 24 hours, as a configurable setting. |Timer set for now|
---

### 14. Requirements Validation and Approval


**Approval:**
Approval means the industry supervisor agrees that these requirements are a fair description of what will be built. It does not remove the need for WIL Coordinator approval.

