const GetSettings = () => {
  return wp.api.loadPromise.then(() => {
    const settings = new wp.api.models.Settings();
    return settings.fetch();
  });
};

export default {
  GetSettings,
};
