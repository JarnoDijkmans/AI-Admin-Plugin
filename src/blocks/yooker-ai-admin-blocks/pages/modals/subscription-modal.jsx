import { useState, useEffect } from 'react';
import {
    __experimentalGrid as Grid,
    __experimentalText as Text,
    __experimentalHeading as Heading,
    Card,
    CardHeader,
    CardBody,
    CardFooter,
    Button,
    FormToggle,
    TextControl,
    TextareaControl,
    __experimentalNumberControl as NumberControl,
    __experimentalSpacer as Spacer,
    Modal
} from '@wordpress/components';
import yookerLogo from "../../../../../assets/images/yooker_icon.png";
import OptionsModelSelector from '../../components/options-model-selector';
import SubscriptionService from '../../services/subscription-service';


const SubscriptionModal = ({ isOpen, onClose, selectedSubscription, refreshSubscriptions }) => {
    if (!isOpen) return null;

    const [subscription, setSubscription] = useState({
        name: '',
        short_description: '',
        price: '',
        version: '1.0.0',
        author: '',
        long_description: '',
        options: [],
        model: []
    });

    useEffect(() => {
        if (isOpen && selectedSubscription) {
            const id = selectedSubscription.id;
            SubscriptionService.getSubscriptionById(id)
                .then((subscriptionData) => {
                    if (subscriptionData) {
                        setSubscription(subscriptionData); 
                    } else {
                        setSubscription([])
                    }
                })
                .catch((error) => {
                    console.error('Error fetching subscription', error);
                })
        }
    }, [isOpen, selectedSubscription]);
    

    const handleChange = (field, value) => {
        setSubscription((prev) => ({
            ...prev,
            [field]: value,
        }));
    };
    
    const handleSave = () => {
        SubscriptionService.saveSubscription(subscription)
            .then((result) => {
                if (result.success) {
                    onClose(); 
                    refreshSubscriptions(); 
                } else {
                    console.error('Error saving subscription:', result.message);
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
    };
    

    return (
        <Modal onRequestClose={onClose}>
            <div>
                <Grid columns={2}>
                    <div>
                        <TextControl
                            label="Abonnement"
                            value={subscription.name}
                            onChange={(value) => handleChange('name', value)}
                        />
                        <TextareaControl
                            label="Korte omschrijving abonnement"
                            value={subscription.short_description}
                            onChange={(value) => handleChange('short_description', value)}
                        />
                        <NumberControl
                            label="Prijs per maand"
                            value={subscription.price}
                            disabled={true}
                        />
                        <Spacer marginBottom={10} />
                        <TextControl
                            label="Version"
                            value={subscription.version}
                            onChange={(value) => handleChange('version', value)}
                            help="Voer de versie in het formaat X.X.X in."
                        />
                        <TextControl
                            label="Author"
                            value={subscription.author}
                            disabled={true}
                        />
                        <TextareaControl
                            label="Detailed omschrijving abonnement"
                            value={subscription.long_description}
                            onChange={(value) => handleChange('long_description', value)}
                        />
                    </div>

                    <div>
                        <OptionsModelSelector 
                            options={subscription.options} 
                            setOptions={(options) => handleChange('options', options)}
                            model={subscription.model}
                            setModel={(model) => handleChange('model', model)} 
                            price={subscription.price}
                            setPrice={(price) => handleChange('price', price)}
                        />

                        <Card style={{ marginRight: '10%' }}>
                            <CardHeader>
                                <Heading level={4}>
                                    <img src={yookerLogo} alt="Yooker Logo" style={{ width: '40px', height: '40px' }} />
                                </Heading>
                            </CardHeader>
                            <CardBody>
                                <Text>{subscription.name}</Text>
                            </CardBody>
                            <CardBody>
                                <Text>{subscription.short_description}</Text>
                            </CardBody>
                            <CardFooter style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                <Button variant="secondary">More info</Button>
                                <div style={{ display: 'flex', alignItems: 'center' }}>
                                    <Text style={{ marginRight: '10px' }}>€{parseFloat(subscription.price).toFixed(2)}</Text>
                                    <FormToggle />
                                </div>
                            </CardFooter>
                        </Card>

                        <Button variant="primary" onClick={handleSave}>
                            Wijzig abonnement
                        </Button>
                    </div>
                </Grid>
            </div>
        </Modal>
    );
};

export default SubscriptionModal;