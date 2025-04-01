import React, { useState, useEffect } from "react";
import { Container, Row, Col, Card, Button, Form, Image } from "react-bootstrap";
import { FaMapMarkerAlt, FaCalendarAlt, FaBell, FaThumbtack, FaBars, FaUserCircle } from "react-icons/fa";
import HomeNavbar from "../components/HomeNavbar";
import Footer from "../components/Footer";
import "./Home.css";

const Home = () => {
  const [events, setEvents] = useState([]);
  const [pinnedEvents, setPinnedEvents] = useState([]);
  const [notifications, setNotifications] = useState([]);
  const [sortBy, setSortBy] = useState("");
  const [isSidebarOpen, setIsSidebarOpen] = useState(true);

  useEffect(() => {
    fetch("https://") // Replace with actual API
      .then((response) => response.json())
      .then((data) => {
        setEvents(data);
        setNotifications(["New events loaded"]);
      })
      .catch((error) => console.error("Error fetching events:", error));
  }, []);

  const handlePinEvent = (event) => {
    setPinnedEvents((prev) => [...prev, event]);
    setNotifications((prev) => [...prev, `Pinned: ${event.title}`]);
  };

  const handleSort = (criteria) => {
    setSortBy(criteria);
    setEvents((prevEvents) =>
      [...prevEvents].sort((a, b) => {
        if (criteria === "date") return new Date(a.date) - new Date(b.date);
        if (criteria === "location") return a.location.localeCompare(b.location);
        return 0;
      })
    );
  };

  return (
    <>
      <HomeNavbar />
      <Container fluid className="home-container">
        <Row>
          {/* Sidebar */}
          <Col md={isSidebarOpen ? 3 : 1} className={`sidebar ${isSidebarOpen ? "open" : "collapsed"}`}>
            {/* Sidebar Header */}
            <div className="sidebar-header w-100">
              <Button variant="light" className="toggle-btn" onClick={() => setIsSidebarOpen(!isSidebarOpen)}>
                <FaBars />
              </Button>
            </div>

            {/* Profile Section */}
            {isSidebarOpen && (
              <div className="profile-section">
                <Image src="/profile.jpg" roundedCircle width={60} height={60} />
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
          </Col>

          {/* Main Content */}
          <Col md={isSidebarOpen ? 9 : 11}>
            <div className="sort-bar">
              <Form.Select value={sortBy} onChange={(e) => handleSort(e.target.value)}>
                <option value="">Sort By</option>
                <option value="date">Date</option>
                <option value="location">Location</option>
              </Form.Select>
            </div>
            <Row>
              {events.map((event) => (
                <Col md={4} key={event.id}>
                  <Card className="event-card">
                    <Card.Img variant="top" src={event.image} alt={event.title} />
                    <Card.Body>
                      <Card.Title>{event.title}</Card.Title>
                      <Card.Text>{event.description}</Card.Text>
                      <p><FaCalendarAlt /> {event.date}</p>
                      <p><FaMapMarkerAlt /> {event.location}</p>
                      <Button variant="primary">View Details</Button>
                      <Button variant="warning" className="ms-2" onClick={() => handlePinEvent(event)}>
                        <FaThumbtack /> Pin
                      </Button>
                    </Card.Body>
                  </Card>
                </Col>
              ))}
            </Row>
          </Col>
        </Row>
      </Container>
      <Footer />
    </>
  );
};

export default Home;
