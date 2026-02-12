export const generateStoreUrl = (routeName: string, store: any, params: any = {}) => {
  return route(routeName, { storeSlug: store?.slug, ...params });
};