import { Button, __experimentalText as Text } from "@wordpress/components";
import { useState, useEffect } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTrash, faPenToSquare } from "@fortawesome/free-solid-svg-icons";
import SubscriptionModal from "../pages/modals/subscription-modal";
import CreateSubscriptionModal from "../pages/modals/create-subscription-modal";
import SubscriptionService from "../services/subscription-service";
import ConfirmModal from "../pages/modals/confirm-modal";

const SubscriptionsManager = () => {
  const [subscriptions, setSubscriptions] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isCreateModalOpen, setCreateModalOpen] = useState(false);
  const [selectedSubscription, setSelectedSubscription] = useState(null);
  const [confirmedOpen, setConfirmedOpen] = useState(false);

  const fetchSubscriptions = () => {
    SubscriptionService.getListSubscriptions()
      .then((subscriptionsData) => {
        if (subscriptionsData) {
          setSubscriptions(subscriptionsData);
        } else {
          setSubscriptions([]);
        }
      })
      .catch((error) => {
        console.error("Error fetching subscriptions:", error);
      });
  };

  useEffect(() => {
    fetchSubscriptions();
  }, []);

  const toggleEdit = (subscription) => {
    setSelectedSubscription(subscription);
    setIsModalOpen(true);
  };

  const closeModal = () => {
    setIsModalOpen(false);
    setSelectedSubscription(null);
  };

  const createNew = () => {
    setCreateModalOpen(true);
  };

  const closeCreateModal = () => {
    setCreateModalOpen(false);
  };

  const removeSubscription = (subscription) => {
    setSelectedSubscription(subscription);
    setConfirmedOpen(true);
  };

  const handleModalClose = (confirmed) => {
    setConfirmedOpen(false);
    if (confirmed && selectedSubscription) {
      SubscriptionService.removeSubscription(selectedSubscription.id)
        .then((response) => {
          if (response) {
            fetchSubscriptions();
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
        <Text as="h1">Abonnementen</Text>

        <div className="row mb-3">
          <div className="col">
            <strong>#</strong>
          </div>
          <div className="col">
            <strong>Abonnement</strong>
          </div>
          <div className="col">
            <strong>Bewerken</strong>
          </div>
          <div className="col">
            <strong>Verwijderen</strong>
          </div>
        </div>

        {subscriptions.map((subscription, index) => (
          <div className="row mb-3" key={subscription.id}>
            <div className="col">{index + 1}</div>
            <div className="col">{subscription.name}</div>
            <div className="col">
              <Button onClick={() => toggleEdit(subscription)}>
                <FontAwesomeIcon icon={faPenToSquare} />
              </Button>
            </div>
            <div className="col">
              <Button
                isDestructive
                onClick={() => removeSubscription(subscription)}
              >
                <FontAwesomeIcon icon={faTrash} />
              </Button>
            </div>
          </div>
        ))}
      </div>
      <Button variant="primary" onClick={() => createNew()}>
        Nieuw Abonnement
      </Button>

      <SubscriptionModal
        isOpen={isModalOpen}
        onClose={closeModal}
        selectedSubscription={selectedSubscription}
        refreshSubscriptions={fetchSubscriptions}
      />

      <CreateSubscriptionModal
        isOpen={isCreateModalOpen}
        onClose={closeCreateModal}
        refreshSubscriptions={fetchSubscriptions}
      />

      <ConfirmModal
        isOpen={confirmedOpen}
        onClose={handleModalClose}
        type={"abonnement"}
        object={selectedSubscription}
      />
    </>
  );
};

export default SubscriptionsManager;
