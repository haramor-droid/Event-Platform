# Survey Summary

User feedback survey for the Event Discovery and Management Platform, collected on Microsoft Forms from 30 October to 3 November 2026.

## About the responses

- 28 responses received and 26 used. Two test responses were excluded.
- 23 attendees. 6 people also answered as organisers (a promoter, two societies, two independent creators and one other), 3 of whom also attend events.
- Most respondents are young phone users reached through friends and social media. The results are indicative and not representative.
- The raw export is not stored in this repository until responses are done.

## Attendees (23)

- 11 often miss events they would have liked to attend, 7 sometimes, 3 rarely and 1 never. One person skipped the question.
- They find events through Instagram (21), WhatsApp (20), TikTok and word of mouth (19 each) and posters (10). A few also named X (2) and Facebook (1).
- Most popular event types: Music and Nightlife (19), Networking (14), Markets and Food (12), Sports and Fitness (9), Workshops and Learning (9), Arts and Culture (7), Community and Charity (3), Family and Kids (2).
- First thing they need to see: price (11), date and time (7), description (2), venue name (1), how to get there (1), poster and photos (1).
- Biggest frustrations in their own words, grouped by hand: cost (5), not finding events because they are poorly promoted or spread across many pages (5), too little detail on listings (2). 3 said nothing frustrates them and 6 skipped the question.
- If an event changes, 9 want a phone notification and 6 want an SMS. Email (3), a site notification (2) and WhatsApp (2) follow, and 1 would check the page themselves.
- 17 would create an account, 1 only after finding 3 relevant events, 4 would not and 1 said not right now.
- 14 mostly use a phone, 8 use both and 1 uses only a laptop. Speed on limited data is extremely important to 15, somewhat important to 5 and neutral for 3.

Ranking of features (average position, 1 is most useful):

| Feature | Average rank |

| Being told if an event is cancelled or changed | 2.4 |
| Suggestions based on interests | 3.0 |
| Reminders before an event | 3.7 |
| Adding to your calendar | 4.6 |
| Saving events | 4.6 |
| Searching and filtering | 4.7 |
| Seeing regular weekly sessions | 5.0 |

## Organisers (6)

- 3 run regular events. For listing them, 3 would like a repeating series listed once, 1 would like each date listed separately and 2 have no preference. Of the 3 who run regular events, one wants a single series, one wants each date separately and one has no preference.
- 5 accept admin approval, but 4 of those said only if it is quick. 1 said no. 5 chose 24 hours as the longest wait and the one who said no chose 2 to 3 days.
- All 6 want to fix details themselves without a review. Details change rarely for 4 and sometimes for 2.
- 4 said engagement statistics would be extremely useful, 1 somewhat useful and 1 somewhat not useful.
- Everyone promotes on WhatsApp. Instagram (4) is next, then TikTok, word of mouth and posters (2 each).
- The hardest part of promoting is reaching new people (5), then tracking interest (1).
- Ticket sales: at the door (3), free events (2), bank transfer or WhatsApp (2), online ticket platform (2).
- Paying a small fee: 3 yes, 2 maybe, 1 no. 5 chose the under N$50 to N$100 option and 1 said nothing.
- What would stop them using the platform: cost (2) and not seeing results (2). One said security issues, one said nothing and one did not know.

## What changed in the requirements (v0.3)

| Finding | Change |

| Price matters as much as date and time | Price required on every listing (BR-20, OR-02, UR-05) |
| Some people want to look before joining | Browsing needs no account (BR-18, UR-03) |
| Phone notifications preferred | Web push is a priority research item (UR-16, OQ-03) |
| Organisers expect a quick review | 24 hour review target (BR-19, OQ-14) |
| Organisers value statistics | Basic counters moved to Should Have (OR-16, SYS-09) |
| Word of mouth and WhatsApp are the main channels | Share button added (UR-18) |
| Most organisers sell at the door or are free | Ticket link can be a note on how to get tickets (OR-08) |
| Cost and results are the worries | Posting stays free for the MVP (OQ-02) |

## Not changed

- Recurring events stay Should Have. 3 of 6 organisers run regular events, but attendees ranked seeing weekly sessions last.
- Editable details rule confirmed. All 6 organisers want to fix details themselves.
- Mobile first confirmed. 22 of 23 attendees use a phone at least some of the time.
- Search and filter stays Must Have, even though it ranked sixth of seven. It is likely seen as expected.
- Admin approval stays, with the 24 hour target. 5 of 6 organisers accept it.

## What changes in the requirements (v0.4)

| Finding | Change |
s
| Price is the first thing 11 of 23 attendees look for | No change needed, BR-20 already covers it |
| 9 of 23 want a phone notification and 6 want SMS | Update OQ-03, and keep SMS on the research list alongside web push |
| Family and Kids was chosen by 2 attendees | Consider adding it to the starting categories (OQ-09) |
| 3 of 6 organisers would pay and 5 of 6 picked under N$50 to N$100 | Add to OQ-02, posting stays free for the MVP |
| One organiser said no to admin approval and one raised security | Check the review process (BR-19) and the security requirements |
| The survey now has 26 usable responses | Update the limitations in the spec from 12 to 26 |

## Ideas for later

- Show how many people have saved an event.
- A leaderboard of popular organisers.
- A light and dark theme.
- Directions to the event and a view of events happening nearby.
- Contact details or a booking link on the listing.
- A clean, attractive and friendly interface (2 people).

## Limitations

- Small sample from one social group.
- Only 6 organisers answered, so the organiser results are even more indicative than the attendee results.
- The ranking assumes the first item listed is the most useful.
- Two people picked an unlabelled "Option 5" as their organiser type. One of them answered N/A to the promotion and ticket questions and is not counted as an organiser.
- Only one organiser answered which listing details would be hard to provide, so that question tells us little.
- The first four attendees were also shown the organiser questions before the form routing was fixed.

## Next

- Only 6 organisers have answered, so ask more to complete the form or talk to two of them directly about approval and fees.