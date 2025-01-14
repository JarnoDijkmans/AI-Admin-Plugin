import {
  TextControl,
  Flex,
  Button,
  __experimentalGrid as Grid,
} from "@wordpress/components";
import { useState, useEffect } from "@wordpress/element";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faEye, faEyeSlash } from "@fortawesome/free-solid-svg-icons";
import AdminOptions from "../services/settings";
import GrippService from "../services/gripp-service";

const settings = () => {
  const [isAPILoaded, setIsAPILoaded] = useState(false);
  const [grippAPIKEY, setGrippAPIKEY] = useState("");
  const [isPasswordVisible, setIsPasswordVisible] = useState(false);
  const [companyName, setCompanyName] = useState("");
  const [clientId, setClientId] = useState(0);

  useEffect(() => {
    fetchOptions();
  }, [isAPILoaded]);

  const fetchOptions = () => {
    if (!isAPILoaded) {
      AdminOptions.GetSettings()
        .then((response) => {
          console.log("retrieval: ", response);
          setGrippAPIKEY(response.grippAPIKEY);
          setIsAPILoaded(true);
        })
        .catch((error) => {
          console.error("Failed to load API settings:", error);
        });
    }
  };

  const changeOptions = (option, value) => {
    const settings = new wp.api.models.Settings({
      [option]: value,
    });
    console.log("before saving: ", settings);
    settings
      .save()
      .then(() => {})
      .catch((error) => {
        console.error("Error saving settings:", error);
      });
  };

  const togglePasswordVisibility = () => {
    setIsPasswordVisible((prevState) => !prevState);
  };

  const handleFindGrippID = () => {
    GrippService.findIdByName(companyName).then((result) => {
      setClientId(result);
    });
  };

  return (
    <div style={{ paddingLeft: "20%", paddingRight: "20%" }}>
      <Flex>
        <div style={{ flex: 1 }}>
          <TextControl
            label="Gripp API Key"
            value={grippAPIKEY}
            id="gripp_api_key"
            onChange={(nextValue) => {
              setGrippAPIKEY(nextValue);
              changeOptions("grippAPIKEY", nextValue);
            }}
            type={isPasswordVisible ? "text" : "password"}
          />
        </div>
        <Button
          onClick={togglePasswordVisibility}
          aria-label={isPasswordVisible ? "Hide AI Key" : "Show AI Key"}
        >
          {isPasswordVisible ? (
            <FontAwesomeIcon icon={faEyeSlash} />
          ) : (
            <FontAwesomeIcon icon={faEye} />
          )}
        </Button>
      </Flex>
      <Flex>
        <div style={{ flex: 1 }}>
          <TextControl
            label="Find gripp client id by company name: "
            value={companyName}
            id="gripp_find_id"
            onChange={(nextValue) => {
              setCompanyName(nextValue);
            }}
          />
        </div>
        <Button
          onClick={handleFindGrippID}
          disabled={!companyName || companyName.trim() === ""}
          style={{
            border: "1px solid gray",
            background: "#ffffff",
            cursor:
              companyName && companyName.trim() !== "" ? "pointer" : "default",
          }}
        >
          Find ID
        </Button>
      </Flex>
      <TextControl disabled value={clientId} />
    </div>
  );
};

export default settings;
