import {
  Button,
  __experimentalText as Text,
  __experimentalGrid as Grid,
} from "@wordpress/components";
import { useState, useEffect } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faTrash,
  faPenToSquare,
  faCheck,
  faXmark,
} from "@fortawesome/free-solid-svg-icons";
import UserModal from "../pages/modals/user-modal";
import UserService from "../services/user-service";
import CreateUserModal from "../pages/modals/create-user-modal";
import ConfirmModal from "../pages/modals/confirm-modal";

const AiGebruikers = () => {
  const [users, setUsers] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const [createModalOpen, setCreateModalOpen] = useState(false);
  const [confirmedOpen, setConfirmedOpen] = useState(false);

  const fetchUsers = () => {
    UserService.getListAiSubscribers()
      .then((userdata) => {
        if (userdata) {
          setUsers(userdata);
        } else {
          setUsers([]);
        }
      })
      .catch((error) => {
        console.error("Error fetching users", error);
      });
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  const toggleEdit = (user) => {
    setSelectedUser(user);
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setSelectedUser(null);
  };

  const closeCreateModal = () => {
    setCreateModalOpen(false);
  };

  const createNew = () => {
    setCreateModalOpen(true);
  };

  const removeUser = (user) => {
    setSelectedUser(user);
    setConfirmedOpen(true);
  };

  const handleModalClose = (confirmed) => {
    setConfirmedOpen(false);
    if (confirmed && selectedUser) {
      UserService.removeUser(selectedUser.user_id)
        .then((response) => {
          if (response) {
            fetchUsers();
          }
        })
        .catch((error) => {
          console.error(
            "Something went wrong deleting user:",
            selectedUser.user_id,
            error
          );
        });
    }
  };

  return (
    <>
      <div className="container">
        <Grid columns={6} gap={8} style={{ marginBottom: "16px" }}>
          <Text as="strong">id</Text>
          <Text as="strong">Gebruiker</Text>
          <Text as="strong">Plaats</Text>
          <Text as="strong">Bewerken</Text>
          <Text as="strong">Activated</Text>
          <Text as="strong">Verwijderen</Text>
        </Grid>

        {users.map((user) => (
          <Grid
            columns={6}
            gap={8}
            key={user.user_id}
            style={{ marginBottom: "8px", alignItems: "center" }}
          >
            <Text>{user.user_id}</Text>
            <Text>{user.company}</Text>
            <Text>{user.address}</Text>
            <Button onClick={() => toggleEdit(user)}>
              <FontAwesomeIcon icon={faPenToSquare} />
            </Button>
            {user.activated === "1" ? (
              <FontAwesomeIcon icon={faCheck} style={{ color: "#19be1b" }} />
            ) : (
              <FontAwesomeIcon icon={faXmark} style={{ color: "#ea1a1a" }} />
            )}
            <Button isDestructive onClick={() => removeUser(user)}>
              <FontAwesomeIcon icon={faTrash} />
            </Button>
          </Grid>
        ))}
      </div>
      <Button variant="primary" onClick={() => createNew()}>
        Nieuw User
      </Button>

      <UserModal
        isOpen={isModalOpen}
        onClose={closeModal}
        selectedUser={selectedUser}
        refreshUsers={fetchUsers}
      />

      <CreateUserModal
        isOpen={createModalOpen}
        onClose={closeCreateModal}
        refreshUsers={fetchUsers}
      />

      <ConfirmModal
        isOpen={confirmedOpen}
        onClose={handleModalClose}
        type="gebruiker"
        object={selectedUser}
      />
    </>
  );
};

export default AiGebruikers;
