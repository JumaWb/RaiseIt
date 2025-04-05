import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import "bootstrap/dist/css/bootstrap.min.css"; // Bootstrap styles
import LandingPage from "./pages/LandingPage";
import Home from "./pages/Home";
import VerificationForm from "./components/VerificationForm";

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<LandingPage />} />
        <Route path="/home" element={<Home />} />
        <Route path="/verify" element={<VerificationForm />} />
      </Routes>
    </Router>
  );
}

export default App;
