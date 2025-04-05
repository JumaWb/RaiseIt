import React, { useState } from "react";
import { Button, Card, Image } from "react-bootstrap";
import { FaThumbtack, FaBell, FaBars, FaUserCircle } from "react-icons/fa";
import "./SideBar.css"; // Ensure Sidebar.css exists

const Sidebar = ({ pinnedEvents, notifications }) => {
  const [isSidebarOpen, setIsSidebarOpen] = useState(true);
  const [profileImage, setProfileImage] = useState(
    localStorage.getItem("profileImage") || "/default-profile.jpg"
  );

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setProfileImage(reader.result);
        localStorage.setItem("profileImage", reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  return (
    <div className={`sidebar ${isSidebarOpen ? "open" : "collapsed"}`}>
      {/* Sidebar Header */}
      <div className="sidebar-header w-100">
        <Button variant="light" className="toggle-btn" onClick={() => setIsSidebarOpen(!isSidebarOpen)}>
          <FaBars />
        </Button>
      </div>

      {/* Profile Section */}
      {isSidebarOpen && (
        <div className="profile-section">
          <label htmlFor="profile-upload" className="profile-upload-label">
            <Image src={profileImage} roundedCircle className="profile-img" />
          </label>
          <input
            type="file"
            id="profile-upload"
            accept="image/*"
            onChange={handleImageChange}
            style={{ display: "none" }}
          />
          <p className="profile-name">John Doe</p>
        </div>
      )}

      {/* Sidebar Content */}
      {isSidebarOpen ? (
        <>
          {/* Pinned Events */}
          <h5><FaThumbtack className="me-2" /> Pinned Events</h5>
          {pinnedEvents.length === 0 ? (
            <p className="text-muted">No pinned events</p>
          ) : (
            pinnedEvents.map((event) => (
              <Card key={event.id} className="pinned-event-card">
                <Card.Img variant="top" src={event.image} />
                <Card.Body>
                  <Card.Title className="small">{event.title}</Card.Title>
                </Card.Body>
              </Card>
            ))
          )}

          {/* Notifications */}
          <h5><FaBell className="me-2" /> Notifications</h5>
          <ul className="list-unstyled">
            {notifications.map((note, index) => (
              <li key={index}>{note}</li>
            ))}
          </ul>
        </>
      ) : (
        <div className="collapsed-icons">
          <FaUserCircle className="sidebar-icon" />
          <FaThumbtack className="sidebar-icon" />
          <FaBell className="sidebar-icon" />
        </div>
      )}
    </div>
  );
};

export default Sidebar;
