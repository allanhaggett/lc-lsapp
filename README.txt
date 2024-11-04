LSApp
Learning Support Administration Application

 **        ********     **                    
/**       **//////     ****    ******  ****** 
/**      /**          **//**  /**///**/**///**
/**      /*********  **  //** /**  /**/**  /**
/**      ////////** **********/****** /****** 
/**             /**/**//////**/**///  /**///  
/******** ******** /**     /**/**     /**     
//////// ////////  //      // //      //      


010011001010010100000011100001110000 



 _        ______ _______             
(_)      / _____(_______)            
 _      ( (____  _______ ____  ____  
| |      \____ \|  ___  |  _ \|  _ \ 
| |_____ _____) | |   | | |_| | |_| |
|_______(______/|_|   |_|  __/|  __/ 
                        |_|   |_|    

						
						
						

"Meta-ELM" It's not an LMS/ELM. It doesn't manage registration, course content delivery, or any interface for the learner.
It does contain all of the data associated with courses and class sessions that is necessary to be able to input that information
into the LMS (ELM). A small team cannot possible memorize the information associated with many dozens of courses.

LSApp integrates Development, Delivery, and Operations with a central repository of meta data and communications 
for all aspects of course creation and delivery.

LSApp answers questions. It exposes the database that operations uses. 
Any and all bits of information that are associated with that course/class are at your finger tips.
If you look at a class page, and there's an important bit of information missing or incorrect? 
You can bring it to LSA attention, and have the record of it maintained.

- Custom Content Management System created by Allan
- General tools suck. Long Live Bespoke! 
	- I didn't write an LMS, I wrote "Learning Centre" software
- Web-based (Bellini! where all the course content lives) and mobile-friendly
- Focused around people and tracking the things they contribute
	- Integrated with Single Sign-On (no new accounts/passwords)
	- Simple Access Control List
		- Roles: super, admin, internal, external
Each course gets its own page, listing _all_ associated meta data, materials, checklists, and upcoming classes
Each class gets its own page listing _everything_ about that class WWWWWhy, including notes and change history
Changes are submitted and tracked on the course/class pages, in context
Basic work flow for entering into the Learning System
Basic venues management
Materials inventory management
Materials order management, tracking, and reporting

Basic (very) checklist management
Basic custom work flows:
	- Venue booking (communications templates, costs)
	- Shipping (labels, communications templates)
Integrates with Learning System (weekly course stats import):
	- Status updates
	- Enrollment numbers
	- Audit tool
Dashboards
	- Upcoming classes
	- Incomplete change requests
	- Unclaimed service requests
	- Person dashboards list every pertinent thing for that person

##ROADMAP


Note: naming all this stuff is NO FUN. Both "session" and "class" are, like, protected words in programming.

# DATA FILE HEADERS and number maps
-----------------courses.csv--------------------
CourseID,Status,CourseName,CourseShort,ItemCode,ClassTimes,ClassDays,ELM,PreWork,PostWork,
CourseOwner,MinMax,CourseNotes,Requested,RequestedBy,EffectiveDate,CourseDescription,CourseAbstract,
Prerequisites,Keywords,Category,Method,elearning,WeShip,ProjectNumber,Responsibility,ServiceLine,STOB,MinEnroll,MaxEnroll

// TO ADD: ReminderDate,LastEnrollDate,LastWaitlistEnroll,LastDropDate
// The above fields should be set as a simple number of days e.g. 7 to indicate that 
// the Reminder should go out 7 days before the start date


//0-CourseID,1-Status,2-CourseName,3-CourseShort,4-ItemCode,5-ClassTimes,6-ClassDays,7-ELM,8-PreWork,9-PostWork,10-CourseOwner,
//11-MinMax,12-CourseNotes,13-Requested,14-RequestedBy,15-EffectiveDate,16-CourseDescription,17-CourseAbstract,18-Prerequisites,
//19-Keywords,20-Categories,21-Method,22-elearning,23-WeShip,24-ProjectNumber,25-Responsibility,26-ServiceLine,
//27-STOB,28-MinEnroll,29-MaxEnroll,30-StartTime,31-EndTime,32-Color
//33-Featured,34-Developer,35-EvaluationsLink,36-LearningHubPartner,37-Alchemer,
//38-Topics,39-Audience,40-Levels,41-Reporting
//42-PathLAN,43-PathStaging,44-PathLive,45-PathNIK,46-PathTeams
// 47-isMoodle,48-TaxProcessed,49-TaxProcessedBy,50-ELMCourseID,51-Modified
// 52-Platform, 53-HUBInclude
// still need to implement 54-HubExpirationDate 55-RegistrationLink 56-CourseNameSlug



--------------------classes.csv--------------------
ClassID,Status,Requested,RequestedBy,Dedicated,CourseID,CourseName,ItemCode,StartDate,EndDate,Times,MinEnroll,MaxEnroll,ShipDate,Facilitating,WebinarLink,WebinarDate,CourseDays,Enrolled,ReservedSeats,PendingApproval,Waitlisted,Dropped,VenueID,VenueName,VenueCity,VenueAddress,VenuePostalCode,VenueContactName,VenuePhone,VenueEmail,VenueAttention,RequestNotes,Shipper,Boxes,Weight,Courier,TrackingOut,TrackingIn,AttendanceReturned,EvaluationsReturned,VenueNotified,Modified,ModifiedBy,Assigned,DeliveryMethod,CourseCategory,Region,CheckedBy,ShippingStatus,PickupIn

// As of 2019-10-30
// 0-ClassID,1-Status,2-RequestedOn,3-RequestedBy,4-Dedicated,5-CourseID,6-CourseName,7-ItemCode,8-ClassDate,9-EndDate,10-ClassTimes,
// 11-MinEnroll,12-MaxEnroll,13-ShipDate,14-Facilitating,
// 15-WebinarLink,16-WebinarDate,17-ClassDays,18-Enrollment,19-ReservedSeats,20-pendingApproval,21-Waitlisted,22-Dropped,
// 23-VenueID,24-Venue,25-City,26-Address,27-ZIPPostal,28-ContactName,29-BusinessPhone,30-email,
// 31-VenueAttention,32-Notes,33- Shipper,34-Boxes,35-Weight,36-Courier,37-TrackingOut,38-TrackingIn,
// 39-Attendance,40-EvaluationsReturned,41-VenueNotified,42-Modified,43-ModifiedBy,44-Assigned,
// 45-DeliveryMethod,46-CourseCategory,47-tblClasses.Region,
// 48-CheckedBy,49-ShippingStatus,50-PickupIn,
// 51-avAssigned,venueCost,venueBEO,StartTime,55-EndTime,56-CourseColor



--------------------ELM.csv:--------------------
// 0-"Course Name",1-Class,2-"Start Date",3-Type,4-Facility,5-"Class Status",6-"Min Enroll",
// 7-"Max Enroll",8-Enrolled,9-"Reserved Seats",10-"Pending Approval",11-Waitlisted,12-Dropped,
// 13-Denied,14-Completed,15-"Not Completed",16-"In-Progress",17-"Planned",18-"Waived",19-"City"


--------------------venues.csv--------------------
0-VenueID,1-VenueName,2-ContactName,3-BusinessPhone,4-Address,5-City,6-StateProvince,7-ZIPPostal,8-email,9-Notes,10-Active,11-Union,12-Region



-------------------changes-course.csv-------------------
0-creqID,1-CourseID,2-CourseName,3-DateRequested,4-RequestedBy,5-Status,6-CompletedBy,7-CompletedDate,8-Request