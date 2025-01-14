import { useState, useEffect, useRef } from "react";
import {
  Modal,
  TextControl,
  Button,
  __experimentalText as Text,
  __experimentalGrid as Grid,
  Notice,
} from "@wordpress/components";
import userService from "../../services/user-service";
import subscriptionService from "../../services/subscription-service";

const UserModal = ({ isOpen, onClose, selectedUser, refreshUsers }) => {
  if (!isOpen) return null;

  const [user, setUser] = useState({
    id: "",
    company: "",
    email: "",
    address: "",
    town: "",
    zipcode: "",
    phonenumber: "",
    apiKey: "",
    first_name: "",
    last_name: "",
    surnameprefix: "",
    subscriptions: [],
    clientnumber_gripp: "",
    activated: "0",
  });
  const [message, setMessage] = useState("");
  const [noticeVisible, setNoticeVisible] = useState(false);

  useEffect(() => {
    if (isOpen && selectedUser) {
      const id = selectedUser.user_id;
      userService
        .getUserDetailsById(id)
        .then((userData) => {
          if (userData) {
            setUser(userData);
            console.log("retrieved data: ", userData);
          } else {
            setUser([]);
          }
        })
        .catch((error) => {
          console.error("Error fetching subscription", error);
        });
    }
  }, [isOpen, selectedUser]);

  const handleRemoveSubscription = (SubId) => {
    subscriptionService
      .removeSubscriptionForUser(SubId, user.id)
      .then((data) => {
        if (data) {
          onClose();
          refreshUsers();
        }
      })
      .catch((error) => {
        console.error(
          "Something went wrong with deleting a subscription.",
          error
        );
      });
  };

  const handleChange = (field, value) => {
    setUser((prev) => ({
      ...prev,
      [field]: value,
    }));
  };

  const handleSave = () => {
    userService
      .saveUser(user)
      .then((result) => {
        if (result.success) {
          setNoticeVisible(false);
          onClose();
          refreshUsers();
        } else {
          setNoticeVisible(true);
          setMessage(result.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  };

  const handleActivate = () => {
    const updatedUser = {
      ...user,
      activated: user.activated === "0" ? "1" : "0",
    };

    userService
      .activateAccount(updatedUser)
      .then((result) => {
        if (result.success) {
          setUser(updatedUser);
          setNoticeVisible(false);
          onClose();
          refreshUsers();
        } else {
          setNoticeVisible(true);
          setMessage(result.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        setNoticeVisible(true);
        setMessage("An unexpected error occurred.");
      });
  };

  const dismissNotice = () => {
    setNoticeVisible(false);
    setMessage("");
  };

  return (
    <Modal
      onRequestClose={onClose}
      shouldCloseOnClickOutside={false}
      shouldCloseOnEsc={true}
      style={{ paddingRight: "60px", paddingLeft: "60px" }}
    >
      {noticeVisible && (
        <div
          style={{
            position: "sticky",
            top: 0,
            zIndex: 1,
            backgroundColor: "white",
          }}
        >
          <Notice status="error" onRemove={dismissNotice} isDismissible>
            {message}
          </Notice>
        </div>
      )}
      <Grid columns={3}>
        <TextControl
          label="Voornaam"
          value={user.first_name}
          onChange={(value) => handleChange("first_name", value)}
        />
        <TextControl
          label="Tussenvoegsel"
          value={user.surnameprefix}
          onChange={(value) => handleChange("surnameprefix", value)}
        />
        <TextControl
          label="Achternaam"
          value={user.last_name}
          onChange={(value) => handleChange("last_name", value)}
        />
      </Grid>

      <Grid columns={1}>
        <TextControl
          label="Bedrijf"
          value={user.company}
          onChange={(value) => handleChange("company", value)}
        />
        <TextControl
          label="e-mail adres"
          value={user.email}
          onChange={(value) => handleChange("email", value)}
          type="email"
        />
        <TextControl
          label="Adres"
          value={user.address}
          onChange={(value) => handleChange("address", value)}
        />
      </Grid>

      {/* Grid for city and zip code */}
      <Grid columns={2}>
        <TextControl
          label="Stad/Dorp"
          value={user.town}
          onChange={(value) => handleChange("town", value)}
        />
        <TextControl
          label="Postcode"
          value={user.zipcode}
          onChange={(value) => handleChange("zipcode", value)}
          help="Bijvoorbeeld: 1234AB"
        />
      </Grid>

      <Grid columns={2}>
        <TextControl
          label="Telefoon nummer"
          value={user.phonenumber}
          onChange={(value) => handleChange("phonenumber", value)}
          type="tel"
        />
        <TextControl
          label="Gripp klantnummer"
          value={user.clientnumber_gripp}
          onChange={(value) => handleChange("clientnumber_gripp", value)}
        />
      </Grid>

      <div>
        <Text as="h4">Abonnementen</Text>
        <ul>
          {user.subscriptions.length > 0 ? (
            user.subscriptions.map((item, index) => (
              <li key={index}>
                Id: {item.id} - {item.name}{" "}
                {item.end_date ? `|| End date: ${item.end_date}` : ""}
                {item.end_date === null && (
                  <Button
                    isDestructive
                    onClick={() => handleRemoveSubscription(item.id)}
                  >
                    Verwijder
                  </Button>
                )}
              </li>
            ))
          ) : (
            <Text>Geen abonnementen gevonden</Text>
          )}
        </ul>
      </div>
      <Grid>
        <Button variant="primary" onClick={handleSave}>
          Wijzig Gebruiker
        </Button>
        <Button variant="primary" onClick={handleActivate}>
          {user.activated === "1" ? "Deactivate Account" : "Activate Account"}
        </Button>
      </Grid>
    </Modal>
  );
};

export default UserModal;
