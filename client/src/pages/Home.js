import React, { useState } from "react";
import { Container, Row, Col, Card, Button, Form } from "react-bootstrap";
import { FaMapMarkerAlt, FaCalendarAlt } from "react-icons/fa";
import HomeNavbar from "../components/HomeNavbar";
import Footer from "../components/Footer";
import "./Home.css";

// Dummy data
const dummyEvents = [
  {
    id: 1,
    title: "Tech Conference 2025",
    description: "Join top minds in technology for a day of networking and innovation.",
    date: "2025-04-15",
    location: "San Francisco, CA",
    image: "https://via.placeholder.com/300x150",
    type: "upcoming"
  },
  {
    id: 2,
    title: "Live Music Festival",
    description: "Experience live performances from your favorite artists.",
    date: "2025-04-07",
    location: "Austin, TX",
    image: "https://via.placeholder.com/300x150",
    type: "live"
  },
  {
    id: 3,
    title: "Marketing Masterclass",
    description: "Learn the latest in digital marketing strategies.",
    date: "2025-05-10",
    location: "New York, NY",
    image: "https://via.placeholder.com/300x150",
    type: "upcoming"
  },
  {
    id: 4,
    title: "Live Coding Session",
    description: "Watch expert developers solve real-world challenges.",
    date: "2025-04-07",
    location: "Remote",
    image: "https://via.placeholder.com/300x150",
    type: "live"
  },
  {
    id: 5,
    title: "Live Coding Session",
    description: "Watch expert developers solve real-world challenges.",
    date: "2025-04-07",
    location: "Remote",
    image: "https://via.placeholder.com/300x150",
    type: "live"
  },
  {
    id: 4,
    title: "Live Coding Session",
    description: "Watch expert developers solve real-world challenges.",
    date: "2025-04-07",
    location: "Remote",
    image: "https://via.placeholder.com/300x150",
    type: "live"
  },{
    id: 5,
    title: "Live Coding Session",
    description: "Watch expert developers solve real-world challenges.",
    date: "2025-04-07",
    location: "Remote",
    image: "https://via.placeholder.com/300x150",
    type: "live"
  },{
    id: 6,
    title: "Live Coding Session",
    description: "Watch expert developers solve real-world challenges.",
    date: "2025-04-07",
    location: "Remote",
    image: "https://via.placeholder.com/300x150",
    type: "live"
  },
  {
    id: 7,
    title: "Marketing Masterclass",
    description: "Learn the latest in digital marketing strategies.",
    date: "2025-05-10",
    location: "New York, NY",
    image: "https://via.placeholder.com/300x150",
    type: "upcoming"
  },
  {
    id: 8,
    title: "Marketing Masterclass",
    description: "Learn the latest in digital marketing strategies.",
    date: "2025-05-10",
    location: "New York, NY",
    image: "https://via.placeholder.com/300x150",
    type: "upcoming"
  },
];

const Home = () => {
  const [activeType, setActiveType] = useState("upcoming");
  const [events] = useState(dummyEvents);

  const filteredEvents = events.filter(event => event.type === activeType);

  return (
    <>
      <HomeNavbar />
      <Container fluid className="home-container px-4">
        <Row className="align-items-center justify-content-between mb-4">
          <Col md={6}>
            <div className="custom-toggle-wrapper">
              <div className="toggle-options">
                <button
                  className={`toggle-btn ${activeType === "upcoming" ? "active" : ""}`}
                  onClick={() => setActiveType("upcoming")}
                >
                  Upcoming Events
                </button>
                <button
                  className={`toggle-btn ${activeType === "live" ? "active" : ""}`}
                  onClick={() => setActiveType("live")}
                >
                  Live Events
                </button>
                <div className={`underline ${activeType}`} />
              </div>
            </div>
          </Col>

          <Col md={3} className="text-end">
            <Form.Select onChange={(e) => console.log(e.target.value)}>
              <option value="">Sort By</option>
              <option value="date">Date</option>
              <option value="location">Location</option>
            </Form.Select>
          </Col>
        </Row>

        <Row>
          {filteredEvents.map((event) => (
            <Col md={4} key={event.id}>
              <Card className="event-card mb-4">
                <Card.Img variant="top" src={event.image} alt={event.title} />
                <Card.Body>
                  <Card.Title>{event.title}</Card.Title>
                  <Card.Text>{event.description}</Card.Text>
                  <p><FaCalendarAlt /> {event.date}</p>
                  <p><FaMapMarkerAlt /> {event.location}</p>
                  <Button variant="primary">View Details</Button>
                </Card.Body>
              </Card>
            </Col>
          ))}
        </Row>
      </Container>
      <Footer />
    </>
  );
};

export default Home;
